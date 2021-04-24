<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnnecessaryFinalModifierSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff;
use SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\UselessLateStaticBindingSniff;

/**
 * @see \Tests\Feature\Laravel\ClassesTest
 */
final class Classes implements HasValue, HasPercentage, HasAvg, HasMax, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getClassLines());
    }

    public function getPercentage(Collector $collector): float
    {
        return $collector->getLines() > 0 ? $collector->getClassLines() / $collector->getLines() * 100 : 0;
    }

    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageClassLength());
    }

    public function getMax(Collector $collector): string
    {
        return sprintf(' % d', $collector->getMaximumClassLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            //FullyQualifiedClassNameAfterKeywordSniff::class,
            ForbiddenPublicPropertySniff::class,
            ForbiddenSetterSniff::class,
            UnnecessaryFinalModifierSniff::class,
            PropertyDeclarationSniff::class,
            ClassConstantVisibilitySniff::class,
            DisallowLateStaticBindingForConstantsSniff::class,
            ModernClassNameReferenceSniff::class,
            UselessLateStaticBindingSniff::class,
            VisibilityRequiredFixer::class,
            ProtectedToPrivateFixer::class,
        ];
    }
}
