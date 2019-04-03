<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\SubCategory;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class CyclomaticComplexityClasses implements HasValue, HasMax, SubCategory
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%.2f', $publisher->getAverageComplexityPerClass());
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getMaximumClassComplexity());
    }
}
