<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;

/**
 * @internal
 */
final class Functions implements HasValue, HasPercentage, HasAvg, HasInsights
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
        return $collector->getLines() > 0 ? ($collector->getFunctionLines() / $collector->getLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageFunctionLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            UnusedInheritedVariablePassedToClosureSniff::class,
        ];
    }
}
