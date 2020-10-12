<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Application\Composer;

interface Preset
{
    /**
     * Returns the configuration preset.
     *
     * @param \NunoMaduro\PhpInsights\Application\Composer $composer
     *
     * @return array<string, string|int|array>
     */
    public static function get(Composer $composer): array;
}
