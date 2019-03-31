<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Dependencies;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class MethodCallsStatic implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getStaticMethodCalls());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getMethodCalls() > 0 ? ($publisher->getStaticMethodCalls() / $publisher->getMethodCalls()) * 100 : 0;
    }
}
