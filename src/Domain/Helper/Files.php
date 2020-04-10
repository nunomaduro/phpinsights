<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Helper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class Files
{
    /**
     * Return an array of files matching in list.
     *
     * @param array<string> $list
     *
     * @return array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    public static function find(string $basedir, array $list): array
    {
        $files = [];
        $userFinder = false;
        $finder = Finder::create()->in($basedir);
        /** @var string $file */
        foreach ($list as $file) {
            if (is_file($file)) {
                $path = realpath($file);
                if ($path === false) {
                    $path = $file;
                }
                $info = pathinfo($file);
                $files[$path] = new SplFileInfo($path, $info['dirname'], $info['basename']);
                continue;
            }

            $userFinder = true;
            $finder->path($file);
        }

        if ($userFinder) {
            $finder->name('*.php')->files();
            /** @var array<string, SplFileInfo> $files */
            $files = array_merge($files, iterator_to_array($finder, true));
        }

        return $files;
    }
}
