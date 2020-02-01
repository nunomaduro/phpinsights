<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;

final class ComposerFinder
{
    /**
     * @param \NunoMaduro\PhpInsights\Domain\Collector $collector
     *
     * @return string
     */
    public static function contents(Collector $collector): string
    {
        return (string) file_get_contents(self::getPath($collector));
    }

    public static function getPath(Collector $collector): string
    {
        $filePath = $collector->getDir().'/composer.json';

        if (file_exists($filePath)) {
            return $filePath;
        }

        throw new ComposerNotFound('`composer.json` not found.');
    }
}
