<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Dependencies;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class AttributeAccessesNonStatic implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getNonStaticAttributeAccesses());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getAttributeAccesses() > 0 ? ($publisher->getNonStaticAttributeAccesses() / $publisher->getAttributeAccesses() * 100) : 0;
    }
}

