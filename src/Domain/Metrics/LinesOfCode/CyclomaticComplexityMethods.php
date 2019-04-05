<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\SubCategory;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class CyclomaticComplexityMethods implements HasValue, HasMax, SubCategory
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%.2f', $collector->getAverageComplexityPerMethod());
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Collector $collector): string
    {
        return sprintf('%d', $collector->getMaximumMethodComplexity());
    }
}
