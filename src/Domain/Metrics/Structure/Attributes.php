<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;

/**
 * @internal
 */
final class Attributes implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            UnusedPrivateElementsSniff::class,
        ];
    }
}
