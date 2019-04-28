<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Style;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Classes\OpeningBraceSameLineSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\EndFileNoNewlineSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceBeforeCastSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\OpeningFunctionBraceBsdAllmanSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\OpeningFunctionBraceKernighanRitchieSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\CharacterBeforePHPOpeningTagSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowAlternativePHPTagsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseTypeSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SAPIUsageSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\UpperCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ArbitraryParenthesesSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\IncrementDecrementSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Files\IncludingFileSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Operators\OperatorSpacingSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseSpacingSniff;

/**
 * @internal
 */
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
            EndFileNoNewlineSniff::class,
            SideEffectsSniff::class,
            GitMergeConflictSniff::class,
            IncludingFileSniff::class,
            ByteOrderMarkSniff::class,
            LineEndingsSniff::class,
            FunctionClosingBraceSniff::class,
            TraitUseSpacingSniff::class,
            OpeningBraceSameLineSniff::class,
            ObjectOperatorIndentSniff::class,
            ScopeClosingBraceSniff::class,
            DisallowLongArraySyntaxSniff::class,
            LineLengthSniff::class,
            SpaceAfterCastSniff::class,
            SpaceAfterNotSniff::class,
            SpaceBeforeCastSniff::class,
            FunctionCallArgumentSpacingSniff::class,
            OpeningFunctionBraceBsdAllmanSniff::class,
            OpeningFunctionBraceKernighanRitchieSniff::class,
            CharacterBeforePHPOpeningTagSniff::class,
            BacktickOperatorSniff::class,
            DisallowAlternativePHPTagsSniff::class,
            DisallowShortOpenTagSniff::class,
            ForbiddenFunctionsSniff::class,
            LowerCaseConstantSniff::class,
            LowerCaseKeywordSniff::class,
            LowerCaseTypeSniff::class,
            SAPIUsageSniff::class,
            SyntaxSniff::class,
            UpperCaseConstantSniff::class,
            ArbitraryParenthesesSpacingSniff::class,
            DisallowTabIndentSniff::class,
            IncrementDecrementSpacingSniff::class,
            LanguageConstructSpacingSniff::class,
            ScopeIndentSniff::class,
            OperatorSpacingSniff::class,
            CamelCapsMethodNameSniff::class,
            ElseIfDeclarationSniff::class,
            SwitchDeclarationSniff::class,
            UpperCaseConstantNameSniff::class,

        ];
    }
}
