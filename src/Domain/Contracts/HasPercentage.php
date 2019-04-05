<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
interface HasPercentage extends Metric
{
    /**
     * Returns the percentage of the metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     *
     * @return float
     */
    public function getPercentage(Collector $collector): float;
}
