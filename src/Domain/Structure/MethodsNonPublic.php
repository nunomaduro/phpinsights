<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class MethodsNonPublic implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getNonPublicMethods());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getMethods() > 0 ? ($publisher->getNonPublicMethods() / $publisher->getMethods()) * 100 : 0;
    }
}
