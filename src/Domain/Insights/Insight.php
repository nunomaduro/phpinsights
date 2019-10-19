<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Helper\FilesFinder;

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
            $this->excludedFiles = FilesFinder::find(
                (string) getcwd(),
                $exclude
            );
        }
    }

    public function getInsightClass(): string
    {
        return static::class;
    }

    public function shouldSkipFile(string $file): bool
    {
        $filepath = $file;
        if (mb_strpos($this->collector->getDir(), $file) === false) {
            $filepath = $this->collector->getDir() . DIRECTORY_SEPARATOR . $file;
        }

        return array_key_exists($filepath, $this->excludedFiles);
    }
}
