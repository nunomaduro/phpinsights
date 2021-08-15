<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneClassPerFileSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;

/**
 * @see \Tests\Feature\Laravel\ClassesTest
 */
final class Classes implements HasValue, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getClasses());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ForbiddenNormalClasses::class,
            ValidClassNameSniff::class,
            ClassDeclarationSniff::class,
            OneClassPerFileSniff::class,
            SuperfluousInterfaceNamingSniff::class,
            SuperfluousAbstractClassNamingSniff::class,
        ];
    }

    public function getPercentage(Collector $collector): float
    {
        return $collector->getFiles() !== [] ? $collector->getClasses() / count($collector->getFiles()) * 100 : 0;
    }
}
