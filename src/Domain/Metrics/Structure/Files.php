<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Files\IncludingFileSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;

/**
 * @internal
 */
final class Files implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return (string) count($collector->getFiles());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ClosingTagSniff::class,
            EndFileNewlineSniff::class,
            SideEffectsSniff::class,
            IncludingFileSniff::class,
            GitMergeConflictSniff::class,
        ];
    }
}
