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

            $newContent = $tokens->generateCode();
            $fullDiff = $this->differ->diff($oldContent, $newContent);

            $this->processDiff($fullDiff, get_class($fixer), $smartFileInfo);

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

    private function processDiff(string $diff, string $fixerClass, SmartFileInfo $smartFileInfo): void
    {
        $parsedDiff = $this->splitStringByLines($diff);
        // Get first line number & Remove headers of diff
        $currentLineNumber = $this->parseLineNumber($parsedDiff[2]);
        $parsedDiff = array_slice($parsedDiff, 3);

        $headerMessage = "You should change following \n";
        $currentMessage = $headerMessage;
        $hasColor = false;
        foreach ($parsedDiff as $diffLine) {
            if (mb_strpos($diffLine, '@@ ') === 0) {
                $this->errorAndDiffCollector->addErrorMessage(
                    $smartFileInfo,
                    $currentLineNumber,
                    $currentMessage,
                    $fixerClass
                );

                $currentLineNumber = $this->parseLineNumber($diffLine);
                $currentMessage = $headerMessage;
                continue;
            }

            if (mb_strpos($diffLine, '-') === 0) {
                $hasColor = true;
                $currentMessage .= '<fg=red>';
            }
            if (mb_strpos($diffLine, '+') === 0) {
                $hasColor = true;
                $currentMessage .= '<fg=green>';
            }
            $currentMessage .= $diffLine;
            if ($hasColor) {
                $hasColor = false;
                $currentMessage .= '</>';
            }
        }

        $this->errorAndDiffCollector->addErrorMessage(
            $smartFileInfo,
            $currentLineNumber,
            $currentMessage,
            $fixerClass
        );
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

    private function parseLineNumber(string $diffLine): int
    {
        $pattern = '@^(?:\@\@ -)?([^,]+)@i';
        $matches = null;
        preg_match($pattern, $diffLine, $matches);

        return (int) $matches[1];
    }
}
