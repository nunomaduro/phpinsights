<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use ObjectCalisthenics\Sniffs\CodeAnalysis\OneObjectOperatorPerLineSniff;
use ObjectCalisthenics\Sniffs\ControlStructures\NoElseSniff;
use ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff;
use ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PHP_CodeSniffer\Standards\Zend\Sniffs\Debug\CodeAnalyzerSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\RequireShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff;
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
            ObjectOperatorIndentSniff::class,
            ScopeClosingBraceSniff::class,
            UnusedVariableSniff::class,
            CodeAnalyzerSniff::class,
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
            UnusedVariableSniff::class,
            UselessVariableSniff::class,
        ];
    }
}
