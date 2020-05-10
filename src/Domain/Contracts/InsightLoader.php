<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
interface InsightLoader
{
    public const INSIGHT_LOADER_TAG = 'phpinsights.insight_loader';

    /**
     * Check if an Insight class could be load by this loader.
     */
    public function support(string $insightClass): bool;

    /**
     * Loads an insight.
     *
     * @param array<string, int|string|array> $config Related to $insightClass
     */
    public function load(string $insightClass, string $dir, array $config, Collector $collector): void;

    /**
     * Gets all loaded insights.
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]
     */
    public function getLoadedInsights(): array;
}
