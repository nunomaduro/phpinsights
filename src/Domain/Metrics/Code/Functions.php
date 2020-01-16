<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\CallTimePassByReferenceSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\NullableTypeDeclarationSniff;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;

final class Functions implements HasValue, HasPercentage, HasAvg, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getFunctionLines());
    }

    public function getPercentage(Collector $collector): float
    {
        return $collector->getLines() > 0 ? $collector->getFunctionLines() / $collector->getLines() * 100 : 0;
    }

    public function getAvg(Collector $collector): string
    {
        return sprintf('%d', $collector->getAverageFunctionLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            UnusedInheritedVariablePassedToClosureSniff::class,
            UnusedParameterSniff::class,
            CallTimePassByReferenceSniff::class,
            DeprecatedFunctionsSniff::class,
            NullableTypeDeclarationSniff::class,
            StaticClosureSniff::class,
            ForbiddenDefineFunctions::class,
            ForbiddenFunctionsSniff::class,
            NoSpacesAfterFunctionNameFixer::class,
            ReturnAssignmentFixer::class,
            VoidReturnFixer::class,
        ];
    }
}
