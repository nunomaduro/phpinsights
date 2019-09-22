<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Helper\Comparator;

/**
 * @internal
 */
final class PathComparator
{
    public static function areEqual(string $pathA, string $pathB): bool
    {
        return realpath($pathA) === realpath($pathB);
    }
}
