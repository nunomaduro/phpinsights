<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Style;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\CharacterBeforePHPOpeningTagSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowAlternativePHPTagsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseTypeSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SAPIUsageSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ArbitraryParenthesesSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\IncrementDecrementSpacingSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes\ClassInstantiationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\NamespaceSpacingSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\RequireOneNamespaceInFileSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseDoesNotStartWithBackslashSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Operators\SpreadOperatorSpacingSniff;
use SlevomatCodingStandard\Sniffs\PHP\ShortListSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff;

final class Style implements HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ClosingTagSniff::class,
            EndFileNewlineSniff::class,
            SideEffectsSniff::class,
            GitMergeConflictSniff::class,
            ByteOrderMarkSniff::class,
            LineEndingsSniff::class,
            FunctionClosingBraceSniff::class,
            // OpeningBraceSameLineSniff::class,
            ObjectOperatorIndentSniff::class,
            ScopeClosingBraceSniff::class,
            DisallowLongArraySyntaxSniff::class,
            LineLengthSniff::class,
            SpaceAfterCastSniff::class,
            SpaceAfterNotSniff::class,
            // SpaceBeforeCastSniff::class,
            FunctionCallArgumentSpacingSniff::class,
            // OpeningFunctionBraceBsdAllmanSniff::class,
            // OpeningFunctionBraceKernighanRitchieSniff::class,
            CharacterBeforePHPOpeningTagSniff::class,
            BacktickOperatorSniff::class,
            DisallowAlternativePHPTagsSniff::class,
            DisallowShortOpenTagSniff::class,
            LowerCaseConstantSniff::class,
            LowerCaseKeywordSniff::class,
            LowerCaseTypeSniff::class,
            SAPIUsageSniff::class,
            SyntaxSniff::class,
            TrailingArrayCommaSniff::class,
            ArbitraryParenthesesSpacingSniff::class,
            DisallowTabIndentSniff::class,
            IncrementDecrementSpacingSniff::class,
            LanguageConstructSpacingSniff::class,
            // ScopeIndentSniff::class,
            // OperatorSpacingSniff::class,
            CamelCapsMethodNameSniff::class,
            ElseIfDeclarationSniff::class,
            SwitchDeclarationSniff::class,
            UpperCaseConstantNameSniff::class,
            AlphabeticallySortedUsesSniff::class,
            NamespaceSpacingSniff::class,
            // ReferenceUsedNamesOnlySniff::class,
            RequireOneNamespaceInFileSniff::class,
            UnusedUsesSniff::class,
            UseDoesNotStartWithBackslashSniff::class,
            UseSpacingSniff::class,
            SpreadOperatorSpacingSniff::class,
            ShortListSniff::class,
            ParameterTypeHintSpacingSniff::class,
            ReturnTypeHintSpacingSniff::class,
            // MultipleUsesPerLineSniff::class,
            SuperfluousWhitespaceSniff::class,
            DocCommentSpacingSniff::class,
            ClassInstantiationSniff::class,
            BracesFixer::class,
            ClassDefinitionFixer::class,
            EncodingFixer::class,
            FullOpeningTagFixer::class,
            FunctionDeclarationFixer::class,
            NoSpacesInsideParenthesisFixer::class,
            NoTrailingWhitespaceFixer::class,
            NoTrailingWhitespaceInCommentFixer::class,
            SingleBlankLineAtEofFixer::class,
            SwitchCaseSemicolonToColonFixer::class,
            SwitchCaseSpaceFixer::class,
            AlignMultilineCommentFixer::class,
            BinaryOperatorSpacesFixer::class,
            CastSpacesFixer::class,
            FunctionTypehintSpaceFixer::class,
            LowercaseStaticReferenceFixer::class,
            MagicConstantCasingFixer::class,
            MagicMethodCasingFixer::class,
            MethodChainingIndentationFixer::class,
            NativeFunctionCasingFixer::class,
            NativeFunctionTypeDeclarationCasingFixer::class,
            NoBlankLinesAfterClassOpeningFixer::class,
            NoExtraBlankLinesFixer::class,
            NoSinglelineWhitespaceBeforeSemicolonsFixer::class,
            NoSpacesAroundOffsetFixer::class,
            NoTrailingCommaInSinglelineArrayFixer::class,
            NoWhitespaceBeforeCommaInArrayFixer::class,
            NoWhitespaceInBlankLineFixer::class,
            SingleQuoteFixer::class,
            StandardizeNotEqualsFixer::class,
            PhpdocIndentFixer::class,
            PhpdocInlineTagFixer::class,
            PhpdocTrimFixer::class,
            PhpdocVarAnnotationCorrectOrderFixer::class,
            SingleClassElementPerStatementFixer::class,
            SingleImportPerStatementFixer::class,
            OrderedClassElementsFixer::class,
            OrderedImportsFixer::class,
        ];
    }
}
