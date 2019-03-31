<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
interface HasMax extends Metric
{
    /**
     * Returns the max of the metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     *
     * @return string
     */
    public function getMax(Publisher $publisher): string;
}
