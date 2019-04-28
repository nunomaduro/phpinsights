<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\MethodTooBig;

/**
 * @internal
 */
final class Methods implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            MethodTooBig::class,
        ];
    }
}
