<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;

final class Constants implements HasValue, HasInsights
{
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
            ForbiddenDefineGlobalConstants::class,
        ];
    }
}
