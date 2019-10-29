<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Helper\Files;

abstract class Insight implements InsightContract
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    protected $collector;

    /**
     * @var array<string, string|int>
     */
    protected $config;

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    protected $excludedFiles;

    /**
     * Creates an new instance of the Insight.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
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
                (string) getcwd(),
                $exclude
            );
        }
    }

    public function getInsightClass(): string
    {
        return static::class;
    }

    protected function shouldSkipFile(string $file): bool
    {
        $filepath = $file;
        if (mb_strpos($this->collector->getDir(), $file) === false) {
            $filepath = $this->collector->getDir() . DIRECTORY_SEPARATOR . $file;
        }

        return array_key_exists($filepath, $this->excludedFiles);
    }

    /**
     * @param array<string, string|int|float|array> $files File path must be in key.
     *
     * @return array<string, string|int|float|array>
     */
    protected function filterFilesWithoutExcluded(array $files): array
    {
        return array_filter($files, function ($file): bool {
            return $this->shouldSkipFile($file) === false;
        }, ARRAY_FILTER_USE_KEY);
    }
}
