<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use NunoMaduro\PhpInsights\Domain\File as InsightFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * This class allows us to wrap original
 * phpcs sniffs adding custom logic into it.
 */
final class SniffWrapper implements Sniff
{
    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff
     */
    private $sniff;

    public function __construct(Sniff $sniff)
    {
        $this->sniff = $sniff;
    }

    public function register(): array
    {
        return $this->sniff->register();
    }

    public function process(File $file, $stackPtr)
    {
        if ($file instanceof InsightFile && $this->skipFilesFromIgnoreFiles($file)) {
            return;
        }

        return $this->sniff->process($file, $stackPtr);
    }

    private function skipFilesFromIgnoreFiles(InsightFile $file): bool
    {
        $path = $file->getFileInfo()->getRealPath();

        if ($path === false) {
            return false;
        }

        foreach ($this->getIgnoredFilesPath() as $ignoredFilePath) {
            if (self::pathsAreEqual($ignoredFilePath, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Contains the setting for all files which the sniff should ignore.
     *
     * @return array<int, string>
     */
    private function getIgnoredFilesPath(): array
    {
        return $this->sniff->ignoreFiles ?? [];
    }

    private static function pathsAreEqual(string $pathA, string $pathB): bool
    {
        return realpath($pathA) === realpath($pathB);
    }

    /**
     * Returns the sniff which we have wrapped.
     */
    public function getWrappedSniff(): Sniff
    {
        return $this->sniff;
    }
}
