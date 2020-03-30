<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @internal
 */
final class PathShortener
{
    /**
     * @param array<string> $paths
     */
    public static function extractCommonPath(array $paths): string
    {
        $paths = array_values($paths);
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
}
