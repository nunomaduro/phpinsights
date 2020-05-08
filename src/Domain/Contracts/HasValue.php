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
     */
    public function getValue(Collector $collector): string;
}
