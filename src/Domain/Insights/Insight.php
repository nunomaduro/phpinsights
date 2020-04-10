<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Helper\Files;

abstract class Insight implements InsightContract
{
    protected Collector $collector;

    /**
     * @var array<string, string|int>
     */
    protected array $config;

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    protected array $excludedFiles;

    /**
     * Creates an new instance of the Insight.
     *
     * @param  array<string, string|int>  $config
     */
    final public function __construct(Collector $collector, array $config)
    {
        $this->collector = $collector;
        $this->config = $config;
        $this->excludedFiles = [];

        /** @var array<string> $exclude */
        $exclude = $config['exclude'] ?? [];
        if (count($exclude) > 0) {
            $this->excludedFiles = Files::find(
                (string) (getcwd() ?? $collector->getCommonPath()),
                $exclude
            );
        }
    }

    final public function getInsightClass(): string
    {
        return static::class;
    }

    final protected function shouldSkipFile(string $file): bool
    {
        return \array_key_exists($file, $this->excludedFiles);
    }

    /**
     * @param array<string, string|int|float|array> $files File path must be in key.
     *
     * @return array<string, string|int|float|array>
     */
    final protected function filterFilesWithoutExcluded(array $files): array
    {
        return array_filter($files, fn (string $file): bool => ! $this->shouldSkipFile($file), ARRAY_FILTER_USE_KEY);
    }
}
