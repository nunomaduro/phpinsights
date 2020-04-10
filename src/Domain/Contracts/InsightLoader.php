<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

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
     * Create a new instance of insight.
     *
     * @param array<string, int|string|array> $config Related to $insightClass
     */
    public function load(string $insightClass, string $dir, array $config): Insight;
}
