<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * This test class tests if the local file repository works as expected.
 */
final class LocalFilesRepositoryTest extends TestCase
{
    public function testCanIgnoreBladeFiles() : void
    {
        $finder = new Finder();

        $repository = new LocalFilesRepository($finder);
        $repository->within( __DIR__.'/Fixtures/FolderWithBladeFile');

        $files = iterator_to_array($repository->getFiles());

        self::assertEmpty($files);
    }
}
