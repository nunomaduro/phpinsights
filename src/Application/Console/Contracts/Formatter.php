<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Contracts;

use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Results;

/**
 * This interface is used to define the format of the result.
 */
interface Formatter
{
    /**
     * Format the result to the desired format.
     *
     * @param InsightCollection $insightCollection
     * @param string            $dir
     * @param array             $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void;
}
