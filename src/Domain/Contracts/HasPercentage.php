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
     */
    public function getPercentage(Collector $collector): float;
}
