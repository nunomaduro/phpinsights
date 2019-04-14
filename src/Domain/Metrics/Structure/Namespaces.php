<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;

/**
 * @internal
 */
final class Namespaces implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', count($collector->getNamespaces()));
    }

    /**
     * Returns the insights classes applied on the metric.
     *
     * @return string[]
     */
    public function getInsights(): array
    {
        return [
            UseDeclarationSniff::class,
            NamespaceDeclarationSniff::class,
            UselessAliasSniff::class,
        ];
    }
}
