<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Helper;

/**
 * @internal
 */
final class Paths
{
    public static function areEqual(string $pathA, string $pathB): bool
    {
        return realpath($pathA) === realpath($pathB);
    }
}
