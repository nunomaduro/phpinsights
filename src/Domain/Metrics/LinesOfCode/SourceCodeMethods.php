<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class SourceCodeMethods implements HasAvg, HasMax, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageMethodLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        // @todo
        return 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Collector $collector): string
    {
        return sprintf('%d', $collector->getMaximumMethodLength());
    }
}
