<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class SourceCodeFunctions implements HasValue, HasPercentage, HasAvg
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getFunctionLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getLogicalLines() > 0 ? ($publisher->getFunctionLines() / $publisher->getLogicalLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getAverageFunctionLength());
    }
}
