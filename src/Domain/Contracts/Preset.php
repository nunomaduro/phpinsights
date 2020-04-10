<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Application\Composer;

/**
 * @internal
 */
interface Preset
{
    /**
     * Returns the preset name.
     */
    public static function getName(): string;

    /**
     * Returns the configuration preset.
     *
     * @return array<string, string|int|array>
     */
    public static function get(Composer $composer): array;

    /**
     * Determinates if the preset should be applied.
     */
    public static function shouldBeApplied(Composer $composer): bool;
}
