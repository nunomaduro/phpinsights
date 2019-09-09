<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\File as InsightFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Decorates original php-cs sniffs with additional behavior.
 */
final class SniffDecorator implements Sniff, Insight, HasDetails
{
    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff
     */
    private $sniff;

    /** @var array<\NunoMaduro\PhpInsights\Domain\Details> */
    private $errors = [];

    /**
     * @var string
     */
    private $dir;

    public function __construct(Sniff $sniff, string $dir)
    {
        $this->sniff = $sniff;
        $this->dir = $dir;
    }

    /**
     * @return array<string>
     */
    public function register(): array
    {
        return $this->sniff->register();
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $stackPtr
     *
     * @return int|void
     */
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

    /**
     * Returns the details of the insight.
     *
     * @return array<int, \NunoMaduro\PhpInsights\Domain\Details>
     */
    public function getDetails(): array
    {
        return $this->errors;
    }

    /**
     * Checks if the insight detects an issue.
     *
     * @return bool
     */
    public function hasIssue(): bool
    {
        return count($this->errors) !== 0;
    }

    /**
     * Gets the title of the insight.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $sniffClass = $this->getInsightClass();

        $path = explode('\\', $sniffClass);
        $name = (string) array_pop($path);

        $name = str_replace('Sniff', '', $name);

        return ucfirst(
            mb_strtolower(
                trim(
                    (string) preg_replace(
                        '/(?<!\ )[A-Z]/',
                        ' $0',
                        $name
                    )
                )
            )
        );
    }

    /**
     * Get the class name of Insight used.
     *
     * @return string
     */
    public function getInsightClass(): string
    {
        return get_class($this->sniff);
    }

    public function addDetails(Details $details): void
    {
        $this->errors[] = $details;
    }
}
