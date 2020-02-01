<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Helper;

use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class Files
{
    /**
     * Return an array of files matching in list.
     *
     * @param string        $basedir
     * @param array<string> $list
     *
     * @return array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    public static function find(string $basedir, array $list): array
    {
        $finder = Finder::create();
        $finder
            ->in($basedir)
            ->path(array_map(static function (string $path) use ($basedir): string {
                if (is_file($path)) {
                    $realPath = realpath($path);

                    if ($realPath !== false) {
                        $path = $realPath;
                    }
                }
                return str_replace($basedir.DIRECTORY_SEPARATOR, '', $path);
            }, $list))
            ->files();

        return iterator_to_array($finder, true);
    }
}
