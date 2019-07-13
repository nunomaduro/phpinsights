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
    /**
     * @var array<string>
     */
    private $appliedFixers;


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
        $splittedOldContent = $this->splitStringByLines($oldContent);

        $this->appliedFixers = [];
        $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);

        /** @var FixerInterface $fixer */
        foreach ($this->getCheckers() as $fixer) {
            $this->processTokensByFixer($smartFileInfo, $tokens, $fixer);

            if ($tokens->isChanged()) {
                $newContent = $this->splitStringByLines($tokens->generateCode());
                foreach ($splittedOldContent as $lineNumber => $content) {
                    $diff = $this->differ->diff($content, $newContent[$lineNumber]);
                    if ($diff === '') {
                        continue;
                    }
                    $this->errorAndDiffCollector->addErrorMessage(
                        $smartFileInfo,
                        $lineNumber,
                        $this->createErrorMessage($content, $newContent[$lineNumber]),
                        get_class($fixer)
                    );
                }
            }
            Tokens::clearCache();
            // Reinit tokens
            $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);
        }

        if ($this->appliedFixers === []) {
            return $oldContent;
        }

        return $this->differ->diff($oldContent, $tokens->generateCode());
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

        $this->appliedFixers[] = get_class($fixer);
    }

    /**
     * @param string $input
     *
     * @return array<int, string>
     */
    private function splitStringByLines(string $input): array
    {
        $result = \preg_split('/(.*\R)/', $input, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($result === false) {
            throw new \RuntimeException('Unable to split ' . $input);
        }

        return $result;
    }

    private function createErrorMessage(string $oldContent, string $newContent): string
    {
        $oldContent = '<fg=red><fg=red;options=bold>--- </>' . trim($oldContent) . '</>';
        $newContent = '<fg=green><fg=green;options=bold>+++ </>' . trim($newContent) . '</>';

        return $oldContent . ' ' . $newContent;
    }
}
