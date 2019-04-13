<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;

/**
 * @internal
 */
final class SourceCode implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getLogicalLines());
    }

    public function getInsights(): array
    {
        return [
            UnusedVariableSniff::class,
        ];
    }
}
