<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;

/**
 * @internal
 */
final class Constants implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getConstants());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            UpperCaseConstantNameSniff::class,
        ];
    }
}
