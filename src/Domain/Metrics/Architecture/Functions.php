<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff;

final class Functions implements HasValue, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getFunctions());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            FunctionLengthSniff::class,
            MethodArgumentSpaceFixer::class,
        ];
    }
}
