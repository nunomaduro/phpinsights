<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\TraitsUsage;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class Traits implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getTraits());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            TraitsUsage::class,
        ];
    }
}
