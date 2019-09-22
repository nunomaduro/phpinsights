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
     *
     * @param string $insightClass
     *
     * @return bool
     */
    public function support(string $insightClass): bool;

    /**
     * @param string $insight
     * @param string $dir
     * @param array<string, string|array> $config
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Insight
     */
    public function load(string $insight, string $dir, array $config): Insight;
}
