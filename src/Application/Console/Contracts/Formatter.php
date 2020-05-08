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
     * @param array<int, string> $metrics
     */
    public function format(InsightCollection $insightCollection, array $metrics): void;
}
