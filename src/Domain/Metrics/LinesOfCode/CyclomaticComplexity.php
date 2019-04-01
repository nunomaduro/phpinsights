<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityAbuse;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class CyclomaticComplexity implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%.2f', $publisher->getAverageComplexityPerLogicalLine());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            CyclomaticComplexityAbuse::class,
        ];
    }
}
