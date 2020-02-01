<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
interface HasValue extends Metric
{
    /**
     * Returns the result of the metric.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Collector $collector
     *
     * @return string
     */
    public function getValue(Collector $collector): string;
}
