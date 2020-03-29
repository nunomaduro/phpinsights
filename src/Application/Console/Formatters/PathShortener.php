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
     *
     * @return string
     */
    public static function extractCommonPath(array $paths): string
    {
        $paths = array_values($paths);
        $lastOffset = 1;
        $common = DIRECTORY_SEPARATOR;

        while (($index = mb_strpos($paths[0], DIRECTORY_SEPARATOR, $lastOffset)) !== false) {
            $dirLen = $index - $lastOffset + 1;
            $dir = mb_substr($paths[0], $lastOffset, $dirLen);

            foreach ($paths as $path) {
                if (mb_substr($path, $lastOffset, $dirLen) !== $dir) {
                    return $common;
                }
            }

            $common .= $dir;
            $lastOffset = $index + 1;
        }

        $common = mb_substr($common, 0, -1);

        return mb_substr($common, -1) === DIRECTORY_SEPARATOR
            ? $common
            : $common . DIRECTORY_SEPARATOR;
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
