<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ClassTooBig;
use NunoMaduro\PhpInsights\Domain\Insights\BiggerMethod;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class SourceCodeClasses implements HasValue, HasPercentage, HasAvg, HasMax, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getClassLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getLogicalLines() > 0 ? ($collector->getClassLines() / $collector->getLogicalLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageClassLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Collector $collector): string
    {
        return sprintf(' % d', $collector->getMaximumClassLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ClassTooBig::class,
        ];
    }
}
