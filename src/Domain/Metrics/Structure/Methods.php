<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff;

/**
 * @internal
 */
final class Methods implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getMethods());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            MaxNestingLevelSniff::class,
        ];
    }
}
