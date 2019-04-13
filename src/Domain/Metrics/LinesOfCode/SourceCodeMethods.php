<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Insights\MethodTooBig;

/**
 * @internal
 */
final class SourceCodeMethods implements HasAvg, HasMax, HasPercentage, HasInsights
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

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            MethodTooBig::class,
        ];
    }
}
