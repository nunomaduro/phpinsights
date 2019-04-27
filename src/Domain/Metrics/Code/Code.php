<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\MethodTooBig;
use ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff;
use ObjectCalisthenics\Sniffs\CodeAnalysis\OneObjectOperatorPerLineSniff;
use ObjectCalisthenics\Sniffs\ControlStructures\NoElseSniff;
use ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff;
use ObjectCalisthenics\Sniffs\Metrics\PropertyPerClassLimitSniff;
use ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff;
use ObjectCalisthenics\Sniffs\NamingConventions\NoSetterSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Files\IncludingFileSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PHP_CodeSniffer\Standards\Zend\Sniffs\Debug\CodeAnalyzerSniff;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\RequireShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Operators\RequireCombinedAssignmentOperatorSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;

/**
 * @internal
 */
final class Code implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            MethodTooBig::class,
            ObjectOperatorIndentSniff::class,
            ScopeClosingBraceSniff::class,
            SideEffectsSniff::class,
            UnusedVariableSniff::class,
            IncludingFileSniff::class,
            ClosingTagSniff::class,
            CodeAnalyzerSniff::class,
            EndFileNewlineSniff::class,
            // ControlStructureSpacingSniff::class,
            SwitchDeclarationSniff::class,
            UselessSemicolonSniff::class,
            UselessParenthesesSniff::class,
            RequireShortTernaryOperatorSniff::class,
            RequireCombinedAssignmentOperatorSniff::class,
            LanguageConstructSpacingSniff::class,
            SuperfluousWhitespaceSniff::class,
            ReferenceThrowableOnlySniff::class,
            NoElseSniff::class,
            OneObjectOperatorPerLineSniff::class,
            ElementNameMinimalLengthSniff::class,
            MaxNestingLevelSniff::class,
            UnusedPrivateElementsSniff::class,
            PropertyPerClassLimitSniff::class,
            ForbiddenPublicPropertySniff::class,
            NoSetterSniff::class,
            UnusedVariableSniff::class,
            UselessVariableSniff::class,
            UnusedInheritedVariablePassedToClosureSniff::class,
        ];
    }
}
