<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Sniffs;

use NunoMaduro\PhpInsights\Domain\File as InsightFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Decorates original php-cs sniffs with additional behavior.
 */
final class SniffDecorator implements Sniff
{
    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff
     */
    private $sniff;

    /**
     * @var string
     */
    private $dir;

    public function __construct(Sniff $sniff, string $dir)
    {
        $this->sniff = $sniff;
        $this->dir = $dir;
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

    public function getSniff(): Sniff
    {
        return $this->sniff;
    }

    private function skipFilesFromIgnoreFiles(InsightFile $file): bool
    {
        $path = $file->getFileInfo()->getRealPath();

        if ($path === false) {
            return false;
        }

        foreach ($this->getIgnoredFilesPath() as $ignoredFilePath) {
            if (self::pathsAreEqual($this->dir . DIRECTORY_SEPARATOR . $ignoredFilePath, $path)) {
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
        return $this->sniff->exclude ?? [];
    }

    private static function pathsAreEqual(string $pathA, string $pathB): bool
    {
        return realpath($pathA) === realpath($pathB);
    }
}
