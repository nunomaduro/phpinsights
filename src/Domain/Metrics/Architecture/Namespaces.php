<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Namespaces\CompoundNamespaceDepthSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;

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
     * @return array<string>
     */
    public function getInsights(): array
    {
        return [
            NamespaceDeclarationSniff::class,
            UselessAliasSniff::class,
            CompoundNamespaceDepthSniff::class,
        ];
    }
}
