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
     * @return <string, string|int>
     */
    public static function get(): array;
}
