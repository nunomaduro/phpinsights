<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * @internal
 */
final class FixerFileProcessor implements FileProcessorInterface
{
    /**
     * @var array<\PhpCsFixer\Fixer\FixerInterface>
     */
    private $fixers = [];
    /**
     * @var \Symplify\EasyCodingStandard\FileSystem\CachedFileLoader
     */
    private $cachedFileLoader;
    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser
     */
    private $fileToTokensParser;
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Differ
     */
    private $differ;

    public function __construct(
        CachedFileLoader $cachedFileLoader,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileToTokensParser $fileToTokensParser,
        Differ $differ
    ) {
        $this->cachedFileLoader = $cachedFileLoader;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->differ = $differ;
    }

    public function addChecker(FixerInterface $fixer): void
    {
        $this->fixers[] = $fixer;
    }

    /**
     * @return array<\PhpCsFixer\Fixer\FixerInterface>
     */
    public function getCheckers(): array
    {
        return $this->fixers;
    }

    /**
     * {@inheritdoc}
     */
    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $filePath = $smartFileInfo->getRealPath();
        if ($filePath === false) {
            throw new \LogicException('Unable to found file ' . $smartFileInfo->getFilename());
        }
        $oldContent = $this->cachedFileLoader->getFileContent($smartFileInfo);
        $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);

        /** @var FixerInterface $fixer */
        foreach ($this->getCheckers() as $fixer) {
            $this->processTokensByFixer($smartFileInfo, $tokens, $fixer);
            if (! $tokens->isChanged()) {
                continue;
            }

            $this->errorAndDiffCollector->addDiffForFileInfo(
                $smartFileInfo,
                $this->differ->diff($oldContent, $tokens->generateCode()),
                [get_class($fixer)]
            );

            Tokens::clearCache();
            // Reinit tokens
            $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);
        }

        return 'processed';
    }

    private function processTokensByFixer(SmartFileInfo $smartFileInfo, Tokens $tokens, FixerInterface $fixer): void
    {
        try {
            $fixer->fix($smartFileInfo, $tokens);
        } catch (\Throwable $throwable) {
            throw new FixerFailedException(sprintf(
                'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                $smartFileInfo->getRelativeFilePath(),
                get_class($fixer),
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine()
            ), $throwable->getCode(), $throwable);
        }

        if (! $tokens->isChanged()) {
            return;
        }
    }
}
