<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\File as InsightFile;
use NunoMaduro\PhpInsights\Domain\Helper\Files;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Decorates original php-cs sniffs with additional behavior.
 *
 * @internal
 */
final class SniffDecorator implements Sniff, Insight, HasDetails, Fixable
{
    /**
     * @var \PHP_CodeSniffer\Sniffs\Sniff
     */
    private $sniff;

    /** @var array<\NunoMaduro\PhpInsights\Domain\Details> */
    private $errors = [];

    /**
     * @var int
     */
    private $fixedCount = 0;
    /**
     * @var array<string, int>
     */
    private $fixPerFile = [];

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private $excludedFiles;

    public function __construct(Sniff $sniff, string $dir)
    {
        $this->sniff = $sniff;
        $this->excludedFiles = [];
        if (count($this->getIgnoredFilesPath()) > 0) {
            $this->excludedFiles = Files::find(
                $dir,
                $this->getIgnoredFilesPath()
            );
        }
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
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $stackPtr)
    {
        if ($file instanceof InsightFile && $this->skipFilesFromIgnoreFiles($file)) {
            return;
        }

        return $this->sniff->process($file, $stackPtr);
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

    public function incrementFix(): void
    {
        $this->fixedCount++;
    }

    public function addFileFixed(string $file): void
    {
        if (! \array_key_exists($file, $this->fixPerFile)) {
            $this->fixPerFile[$file] = 0;
        }

        $this->fixPerFile[$file] = ++$this->fixPerFile[$file];
        $this->incrementFix();
    }

    public function getTotalFix(): int
    {
        return $this->fixedCount;
    }

    /**
     * @return array<Details>
     */
    public function getFixPerFile(): array
    {
        $details = [];
        foreach ($this->fixPerFile as $file => $count) {
            $message = 'issues fixed';
            if ($count === 1) {
                $message = 'issue fixed';
            }

            $details[] = (new Details())
                ->setMessage(sprintf('%s %s', $count, $message))
                ->setFile($file);
        }

        return $details;
    }

    private function skipFilesFromIgnoreFiles(InsightFile $file): bool
    {
        return array_key_exists(
            (string) $file->getFileInfo()->getRealPath(),
            $this->excludedFiles
        );
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
}
