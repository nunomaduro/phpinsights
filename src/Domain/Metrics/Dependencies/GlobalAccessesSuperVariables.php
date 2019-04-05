<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class GlobalAccessesSuperVariables implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getSuperGlobalVariableAccesses());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getGlobalAccesses() > 0 ? ($collector->getSuperGlobalVariableAccesses() / $collector->getGlobalAccesses()) * 100 : 0;
    }
}
