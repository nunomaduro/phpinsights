<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;

/**
 * @internal
 */
final class Globally implements HasValue, HasPercentage, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getGlobalAccesses());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getGlobalAccesses() > 0 ? ($collector->getGlobalAccesses() / $collector->getGlobalAccesses()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            // ..
        ];
    }
}
