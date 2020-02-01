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
    public static function getInstance(Collector $collector): Composer
    {
        $io = new NullIO();

        return Factory::create($io, ComposerFinder::getPath($collector));
    }
}
