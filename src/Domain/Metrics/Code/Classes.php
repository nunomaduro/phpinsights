<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff;
use ObjectCalisthenics\Sniffs\NamingConventions\NoSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnnecessaryFinalModifierSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes\ClassInstantiationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use SlevomatCodingStandard\Sniffs\Classes\UselessLateStaticBindingSniff;

final class Classes implements HasValue, HasPercentage, HasAvg, HasMax, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getClassLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getLines() > 0 ? ($collector->getClassLines() / $collector->getLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageClassLength());
    }

    /**
     * {@inheritdoc}
     */
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
            ForbiddenPublicPropertySniff::class,
            UnusedPrivateElementsSniff::class,
            NoSetterSniff::class,
            UnnecessaryFinalModifierSniff::class,
            ClassInstantiationSniff::class,
            PropertyDeclarationSniff::class,
            ClassConstantVisibilitySniff::class,
            DisallowLateStaticBindingForConstantsSniff::class,
            ModernClassNameReferenceSniff::class,
            UselessLateStaticBindingSniff::class
        ];
    }
}
