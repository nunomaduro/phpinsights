<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Dependencies;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class GlobalAccessesConstants implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getGlobalConstantAccesses());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getGlobalAccesses() > 0 ? ($publisher->getGlobalConstantAccesses() / $publisher->getGlobalAccesses()) * 100 : 0;
    }
}
