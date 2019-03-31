<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\BiggerClass;
use NunoMaduro\PhpInsights\Domain\Insights\BiggerMethod;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class SourceCodeClasses implements HasValue, HasPercentage, HasAvg, HasMax, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getClassLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getLogicalLines() > 0 ? ($publisher->getClassLines() / $publisher->getLogicalLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getAverageClassLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Publisher $publisher): string
    {
        return sprintf(' % d', $publisher->getMaximumClassLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            BiggerClass::class,
        ];
    }
}
