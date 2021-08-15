<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\DetailsCarrier;
use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
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
 *
 * @see \Tests\Domain\Sniffs\SniffDecoratorTest
 */
final class SniffDecorator implements Sniff, Insight, DetailsCarrier, Fixable
{
    use FixPerFileCollector;

    private Sniff $sniff;

    /** @var array<\NunoMaduro\PhpInsights\Domain\Details> */
    private array $errors = [];

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private array $excludedFiles;

    public function __construct(Sniff $sniff, string $dir)
    {
        $this->sniff = $sniff;
        $this->excludedFiles = [];

        if ($this->getIgnoredFilesPath() !== []) {
            $this->excludedFiles = Files::find($dir, $this->getIgnoredFilesPath());
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
     * @param int $stackPtr
     *
     * @return int|void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
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
     */
    public function hasIssue(): bool
    {
        return $this->errors !== [];
    }

    /**
     * Gets the title of the insight.
     */
    public function getTitle(): string
    {
        $sniffClass = $this->getInsightClass();

        $path = explode('\\', $sniffClass);
        $name = array_pop($path);

        $name = str_replace('Sniff', '', $name);

        return ucfirst(
            mb_strtolower(
                trim(
                    (string) preg_replace(
                        '/(?<! )[A-Z]/',
                        ' $0',
                        $name
                    )
                )
            )
        );
    }

    /**
     * Get the class name of Insight used.
     */
    public function getInsightClass(): string
    {
        return get_class($this->sniff);
    }

    public function addDetails(Details $details): void
    {
        $this->errors[] = $details;
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
