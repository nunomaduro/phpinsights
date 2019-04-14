<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface Preset
{
    /**
     * Returns the preset name.
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Returns the configuration preset.
     *
     * @return array<string, string|int|array>
     */
    public static function get(): array;

    /**
     * Determinates if the preset should be applied.
     *
     * @param  array<string, string|int|array>  $composer
     *
     * @return bool
     */
    public static function shouldBeApplied(array $composer): bool;
}
