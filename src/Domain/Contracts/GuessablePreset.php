<?php

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use NunoMaduro\PhpInsights\Application\Composer;

/**
 * @internal
 */
interface GuessablePreset
{
    /**
     * Returns the preset name.
     */
    public static function getName(): string;

    /**
     * Determines if the preset should be applied.
     */
    public static function shouldBeApplied(Composer $composer): bool;
}
