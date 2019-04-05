<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;

use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class MethodCalls implements HasValue
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getMethodCalls());
    }
}
