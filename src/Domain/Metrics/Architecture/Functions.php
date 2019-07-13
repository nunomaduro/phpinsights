<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;

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
            VoidReturnFixer::class,
            ProtectedToPrivateFixer::class,
        ];
    }
}
