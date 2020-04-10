<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
interface HasMax extends Metric
{
    /**
     * Returns the max of the metric.
     */
    public function getMax(Collector $collector): string;
}
