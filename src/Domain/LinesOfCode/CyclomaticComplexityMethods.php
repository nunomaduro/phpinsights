<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class CyclomaticComplexityMethods implements HasValue, HasMax
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%.2f', $publisher->getAverageComplexityPerMethod());
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getMaximumMethodComplexity());
    }
}
