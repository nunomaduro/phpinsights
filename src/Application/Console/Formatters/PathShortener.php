<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @internal
 *
 * @see \Tests\Application\Console\Formatters\PathShortenerTest
 */
final class PathShortener
{
    /**
     * @param array<string> $paths
     */
    public static function extractCommonPath(array $paths): string
    {
        $paths = array_values($paths);
        $paths = self::sanitizePath($paths);
        sort($paths);

        $first = $paths[0];
        $last = $paths[count($paths) - 1];
        $length = min(\strlen($first), \strlen($last));

        for ($index = 0; $index < $length && $first[$index] === $last[$index];) {
            $index++;
        }

        $prefix = mb_substr($first, 0, $index ?? 0);

        return mb_substr($prefix, 0, (int) mb_strrpos($prefix, DIRECTORY_SEPARATOR) + 1);
    }

    public static function fileName(Details $detail, string $commonPath): string
    {
        if ($detail->hasFile()) {
            $file = $detail->getFile();

            return mb_strpos($file, $commonPath) !== false
                ? str_replace($commonPath, '', $file)
                : $file;
        }

        return '';
    }

    /**
     * @param array<string> $paths
     *
     * @return array<string>
     */
    private static function sanitizePath(array $paths): array
    {
        return array_map(static function ($path): string {
            $path = rtrim($path, DIRECTORY_SEPARATOR);

            return is_dir($path) ? $path . DIRECTORY_SEPARATOR : $path;
        }, $paths);
    }
}
