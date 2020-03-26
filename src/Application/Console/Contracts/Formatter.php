<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Contracts;

use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;

/**
 * This interface is used to define the format of the result.
 *
 * @internal
 */
interface Formatter
{
    /**
     * Format the result to the desired format.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insightCollection
     * @param array<string> $directories
     * @param array<int, string> $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        array $directories,
        array $metrics
    ): void;
}
