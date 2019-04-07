<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\ComposerContainsName;
use NunoMaduro\PhpInsights\Domain\Insights\ComposerExists;

/**
 * @internal
 */
final class Composer implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            ComposerExists::class,
            ComposerContainsName::class,
        ];
    }
}
