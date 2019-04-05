<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ConstantsGlobalUsage;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class ConstantsGlobal implements HasValue, HasPercentage, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', count($collector->getGlobalConstants()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getConstants() > 0 ? (count($collector->getGlobalConstants()) / $collector->getConstants()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ConstantsGlobalUsage::class,
        ];
    }
}
