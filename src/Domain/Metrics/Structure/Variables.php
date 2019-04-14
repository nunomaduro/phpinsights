<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;

/**
 * @internal
 */
final class Variables implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            UnusedVariableSniff::class,
            UselessVariableSniff::class,
            UnusedInheritedVariablePassedToClosureSniff::class,
        ];
    }
}
