<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Files\IncludingFileSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\Zend\Sniffs\Debug\CodeAnalyzerSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\ControlStructureSpacingSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;

/**
 * @internal
 */
final class SourceCode implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getLogicalLines());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
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
        ];
    }
}
