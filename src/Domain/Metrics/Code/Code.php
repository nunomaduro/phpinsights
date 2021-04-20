<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyPHPStatementSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopShouldBeWhileLoopSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopWithTestFunctionCallSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\JumbledIncrementerSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnconditionalIfStatementSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures\InlineControlStructureSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DiscourageGotoSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\NoSilencedErrorsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Keywords\ShortFormTypeKeywordsSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Zend\Sniffs\Debug\CodeAnalyzerSniff;
use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\FunctionNotation\CombineNestedDirnameFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use SlevomatCodingStandard\Sniffs\Arrays\DisallowImplicitArrayCreationSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowContinueWithoutIntegerOperandInSwitchSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\LanguageConstructWithParenthesesSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UselessParameterDefaultValueSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;
use SlevomatCodingStandard\Sniffs\Operators\DisallowEqualOperatorsSniff;
use SlevomatCodingStandard\Sniffs\Operators\RequireCombinedAssignmentOperatorSniff;
use SlevomatCodingStandard\Sniffs\Operators\RequireOnlyStandaloneIncrementAndDecrementOperatorsSniff;
use SlevomatCodingStandard\Sniffs\PHP\OptimizedFunctionsWithoutUnpackingSniff;
use SlevomatCodingStandard\Sniffs\PHP\TypeCastSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\Variables\DuplicateAssignmentToVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;

final class Code implements HasValue, HasInsights
{
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
            UnusedVariableSniff::class,
            CodeAnalyzerSniff::class,
            SwitchDeclarationSniff::class,
            LanguageConstructSpacingSniff::class,
            UselessVariableSniff::class,
            EvalSniff::class,
            ArrayIndentSniff::class,
            EmptyPHPStatementSniff::class,
            EmptyStatementSniff::class,
            ForLoopShouldBeWhileLoopSniff::class,
            ForLoopWithTestFunctionCallSniff::class,
            JumbledIncrementerSniff::class,
            UnconditionalIfStatementSniff::class,
            UselessOverridingMethodSniff::class,
            InlineControlStructureSniff::class,
            DisallowMultipleStatementsSniff::class,
            BacktickOperatorSniff::class,
            DiscourageGotoSniff::class,
            NoSilencedErrorsSniff::class,
            UnnecessaryStringConcatSniff::class,
            ShortFormTypeKeywordsSniff::class,
            DisallowImplicitArrayCreationSniff::class,
            AssignmentInConditionSniff::class,
            DisallowContinueWithoutIntegerOperandInSwitchSniff::class,
            DisallowEmptySniff::class,
            DisallowShortTernaryOperatorSniff::class,
            DisallowYodaComparisonSniff::class,
            LanguageConstructWithParenthesesSniff::class,
            DeadCatchSniff::class,
            UnusedInheritedVariablePassedToClosureSniff::class,
            UselessParameterDefaultValueSniff::class,
            UseFromSameNamespaceSniff::class,
            UselessAliasSniff::class,
            DisallowEqualOperatorsSniff::class,
            RequireCombinedAssignmentOperatorSniff::class,
            RequireOnlyStandaloneIncrementAndDecrementOperatorsSniff::class,
            OptimizedFunctionsWithoutUnpackingSniff::class,
            TypeCastSniff::class,
            UselessParenthesesSniff::class,
            UselessSemicolonSniff::class,
            DeclareStrictTypesSniff::class,
            DuplicateAssignmentToVariableSniff::class,
            // FullyQualifiedExceptionsSniff::class,
            // FullyQualifiedGlobalConstantsSniff::class,
            TernaryToNullCoalescingFixer::class,
            CombineNestedDirnameFixer::class,
            DeclareEqualNormalizeFixer::class,
            ExplicitStringVariableFixer::class,
            NewWithBracesFixer::class,
            NoAlternativeSyntaxFixer::class,
            NoMixedEchoPrintFixer::class,
            NoMultilineWhitespaceAroundDoubleArrowFixer::class,
            NoShortBoolCastFixer::class,
            NoSuperfluousElseifFixer::class,
            NoUnneededControlParenthesesFixer::class,
            NoUselessElseFixer::class,
            NormalizeIndexBraceFixer::class,
            ObjectOperatorWithoutWhitespaceFixer::class,
            ShortScalarCastFixer::class,
            TernaryOperatorSpacesFixer::class,
        ];
    }
}
