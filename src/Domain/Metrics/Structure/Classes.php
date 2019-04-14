<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Classes\DuplicateClassNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff;

/**
 * @internal
 */
final class Classes implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getClasses());
    }

    /**
     * Returns the insights classes applied on the metric.
     *
     * @return string[]
     */
    public function getInsights(): array
    {
        return [
            ValidClassNameSniff::class,
            ClassDeclarationSniff::class,
            DuplicateClassNameSniff::class,
            UnusedPrivateElementsSniff::class,
            ModernClassNameReferenceSniff::class,
        ];
    }
}
