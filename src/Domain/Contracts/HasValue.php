<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
interface HasValue extends Metric
{
    /**
     * Returns the result of the metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     *
     * @return string
     */
    public function getValue(Publisher $publisher): string;
}
