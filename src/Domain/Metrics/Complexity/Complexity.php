<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Complexity;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;

/**
 * @internal
 */
final class Complexity implements HasAvg, HasInsights
{
    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     *
     * @return array
     */
    public function getClassComplexity(Collector $collector): array
    {
        return $collector->getClassComplexity();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%.2f', $collector->getAverageComplexityPerLogicalLine());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            CyclomaticComplexityIsHigh::class
        ];
    }
}
