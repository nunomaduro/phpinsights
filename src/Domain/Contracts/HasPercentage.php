<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
interface HasPercentage extends Metric
{
    /**
     * Returns the percentage of the metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     *
     * @return float
     */
    public function getPercentage(Publisher $publisher): float;
}
