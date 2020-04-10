<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;

/**
 * @see \Tests\Domain\ComposerFinderTest
 */
final class ComposerFinder
{
    public static function contents(Collector $collector): string
    {
        return (string) file_get_contents(self::getPath($collector));
    }

    public static function getPath(Collector $collector): string
    {
        $filePath = $collector->getCommonPath() . 'composer.json';

        if (file_exists($filePath)) {
            return $filePath;
        }

        throw new ComposerNotFound('`composer.json` not found.');
    }
}
