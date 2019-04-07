<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface Preset
{
    /**
     * Returns the `Insights` added by the preset.
     *
     * @return string[]
     */
    public static function adds(): array;

    /**
     * Returns the `Insights` ignored by the preset.
     *
     * @return string[]
     */
    public static function ignores(): array;
}
