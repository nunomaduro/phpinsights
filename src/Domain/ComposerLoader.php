<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Package\Locker;
use Composer\Repository\RepositoryManager;

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
            self::$composer->setLocker(self::getLocker($composerPath, $io));
        }

        return self::$composer;
    }

    private static function getLocker(string $composerPath, NullIO $io): Locker
    {
        if (self::$composer === null) {
            throw new \RuntimeException('Cannot get locker until composer is not initialized');
        }
        $lockFile = pathinfo($composerPath, PATHINFO_EXTENSION) === 'json'
            ? substr($composerPath, 0, -4).'lock'
            : $composerPath . '.lock';

        $composerContent = file_get_contents($composerPath);
        if ($composerContent === false) {
            throw new \InvalidArgumentException('Unable to get content of ' . $composerPath);
        }

        $lockerConstructor = new \ReflectionMethod(Locker::class, '__construct');

        // In composer v2, RepositoryManager is no longer needed
        /** @phpstan-ignore-next-line */
        return $lockerConstructor->getParameters()[2]->getType()->getName() === RepositoryManager::class
            ? new Locker($io, new JsonFile($lockFile, null, $io), self::$composer->getRepositoryManager(), self::$composer->getInstallationManager(), $composerContent)
            : new Locker($io, new JsonFile($lockFile, null, $io), self::$composer->getInstallationManager(), $composerContent); /** @phpstan-ignore-line */
    }
}
