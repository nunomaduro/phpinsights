<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface HasInsights extends Metric
{
    /**
     * Returns the insights classes applied on the metric.
     *
     * @return array<string>
     */
    public function getInsights(): array;
}
