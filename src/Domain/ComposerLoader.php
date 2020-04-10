<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;

/**
 * @internal
 */
final class ComposerLoader
{
    private static ?Collector $currentCollector = null;
    private static ?Composer $composer = null;

    public static function getInstance(Collector $collector): Composer
    {
        if (self::$composer === null || $collector !== self::$currentCollector) {
            self::$currentCollector = $collector;

            $io = new NullIO();
            self::$composer = Factory::create($io, ComposerFinder::getPath($collector));
        }

        return self::$composer;
    }
}
