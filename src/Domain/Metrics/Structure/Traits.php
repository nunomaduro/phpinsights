<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseSpacingSniff;

/**
 * @internal
 */
final class Traits implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', count($collector->getTraits()));
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            ForbiddenTraits::class,
            TraitUseSpacingSniff::class,
        ];
    }
}
