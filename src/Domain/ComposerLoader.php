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
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private static $currentCollector;
    /**
     * @var Composer|null
     */
    private static $composer;

    public static function getInstance(Collector $collector): Composer
    {
        if (null === self::$composer || $collector !== self::$currentCollector) {
            self::$currentCollector = $collector;

            $io = new NullIO();
            self::$composer = Factory::create($io, ComposerFinder::getPath($collector));
        }

        return self::$composer;
    }
}
