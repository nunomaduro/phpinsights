<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
interface HasAvg extends Metric
{
    /**
     * Returns the avg of the metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     *
     * @return string
     */
    public function getAvg(Publisher $publisher): string;
}
