<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface Preset
{
    /**
     * Returns the configuration preset.
     *
     * @return array<string, string|int|array>
     */
    public static function get(): array;
}
