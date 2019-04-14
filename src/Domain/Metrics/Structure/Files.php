<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;

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
            GitMergeConflictSniff::class,
        ];
    }
}
