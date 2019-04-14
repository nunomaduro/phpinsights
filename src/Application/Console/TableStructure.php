<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;
use NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure;

/**
 * @internal
 */
final class TableStructure
{
    /**
     * Returns the table structure.
     *
     * @return string[]
     */
    public static function make(): array
    {
        return [
            '<title>✍️  Lines Of Code</title>',
            '',
            LinesOfCode\Total::class,
            LinesOfCode\Comments::class,
            LinesOfCode\NonComments::class,
            LinesOfCode\SourceCode::class,
            LinesOfCode\SourceCodeClasses::class,
            LinesOfCode\SourceCodeMethods::class,
            LinesOfCode\SourceCodeFunctions::class,
            LinesOfCode\SourceCodeGlobal::class,
            '',
            '<title>👔  Cyclomatic Complexity</title>',
            '',
            Complexity\Complexity::class,
            Complexity\ComplexityClasses::class,
            Complexity\ComplexityMethods::class,
            '',
            '<title>🧱  Structure</title>',
            '',
            Structure\Files::class,
            Structure\Namespaces::class,
            Structure\Interfaces::class,
            Structure\Traits::class,
            Structure\Classes::class,
            Structure\ClassesAbstract::class,
            Structure\ClassesFinal::class,
            Structure\ClassesNormal::class,
            Structure\Methods::class,
            Structure\MethodsStatic::class,
            Structure\MethodsNonStatic::class,
            Structure\MethodsPublic::class,
            Structure\MethodsProtected::class,
            Structure\MethodsPrivate::class,
            Structure\Functions::class,
            Structure\FunctionsGlobalHelpers::class,
            Structure\FunctionsAnonymous::class,
            Structure\Constants::class,
            Structure\ConstantsGlobal::class,
            Structure\ConstantsClass::class,
            Structure\Composer::class,
            Structure\Attributes::class,
            Structure\Variables::class,
            '',
            '<title>🔗  Dependencies</title>',
            '',
            Dependencies\GlobalAccesses::class,
            Dependencies\GlobalAccessesConstants::class,
            Dependencies\GlobalAccessesVariables::class,
            Dependencies\GlobalAccessesSuperVariables::class,
            Dependencies\AttributeAccesses::class,
            Dependencies\AttributeAccessesStatic::class,
            Dependencies\AttributeAccessesNonStatic::class,
            Dependencies\MethodCalls::class,
            Dependencies\MethodCallsStatic::class,
            Dependencies\MethodCallsNonStatic::class,
        ];
    }
}
