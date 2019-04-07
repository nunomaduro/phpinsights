<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Presets;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset;
use NunoMaduro\PhpInsights\Domain\Insights\TraitsUsage;

/**
 * @internal
 */
final class Laravel implements Preset
{
    /**
     * {@inheritDoc}
     */
    public static function adds(): array
    {
        return [
            // ..
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function ignores(): array
    {
        return [
            TraitsUsage::class,
        ];
    }
}
