<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Package\Locker;

/**
 * @internal
 *
 * @see \Tests\Domain\ComposerLoaderTest
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
            $composerPath = ComposerFinder::getPath($collector);
            self::$composer = (new Factory())->createComposer($io, ComposerFinder::getPath($collector), false, null, false);

            $lockFile = pathinfo($composerPath, PATHINFO_EXTENSION) === 'json'
                ? substr($composerPath, 0, -4).'lock'
                : $composerPath . '.lock';

            $composerContent = file_get_contents($composerPath);
            if ($composerContent === false) {
                throw new \InvalidArgumentException('Unable to get content of ' . $composerPath);
            }

            $locker = new Locker($io, new JsonFile($lockFile, null, $io), self::$composer->getRepositoryManager(), self::$composer->getInstallationManager(), $composerContent);
            self::$composer->setLocker($locker);
        }

        return self::$composer;
    }
}
