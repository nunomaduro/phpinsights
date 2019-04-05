<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class SourceCodeFunctions implements HasValue, HasPercentage, HasAvg
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getFunctionLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getLogicalLines() > 0 ? ($collector->getFunctionLines() / $collector->getLogicalLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageFunctionLength());
    }
}
