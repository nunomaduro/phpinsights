<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

final class LocalFilesRepositoryTest extends TestCase
{
    private const BASE = __DIR__ . '/../../Fixtures/Tree';

    /**
     * @dataProvider provider
     * @param int $expected
     * @param array<string> $exclude
     */
    public function testItExcludesFilesGivenAPath(int $expected, array $exclude): void
    {
        $repository = new LocalFilesRepository(Finder::create());

        $files = $repository->within([self::BASE], $exclude)->getFiles();

        self::assertCount($expected, $files);
    }
    /**
     * @return array<int, array<int, array<int, string>|int>>
     */
    public function provider(): array
    {
        return [
            [3, ['FolderA/ClassA.php']],
            [4, []],
            [2, ['ClassA.php']],
            [2, ['FolderA']],
            [1, ['FolderA', 'FolderB/ClassB.php']],
            [3, ['FolderA/SubFolderA']],
            [3, ['FolderA/SubFolderA/ClassC.php']],
            [2, ['/(\w).*(A.php)$/']],
            [2, ['/((\w).*)?(FolderA\/)(\w).*/']]
        ];
    }
    public function testDefaultDirectory(): void
    {
        $finder = new Finder();

        $repository = new LocalFilesRepository($finder);

        self::assertSame((string) getcwd(), $repository->getDefaultDirectory());
    }
    public function testCanIgnoreBladeFiles(): void
    {
        $finder = new Finder();

        $repository = new LocalFilesRepository($finder);
        $repository->within([__DIR__ . '/Fixtures/FolderWithBladeFile']);

        $files = $repository->getFiles();

        self::assertEmpty($files);
    }
    public function testPassFileInsteadOfDirectory(): void
    {
        $finder = new Finder();

        $repository = new LocalFilesRepository($finder);
        $repository->within([__DIR__ . '/Fixtures/FileToInspect.php']);
        $files = array_values($repository->getFiles());

        self::assertCount(1, $files);
        self::assertInstanceOf(SplFileInfo::class, $files[0]);
        $path = $files[0]->getRealPath();

        if ($path === false) {
            self::fail('Path cannot be false.');
        } else {
            self::assertStringContainsString('/Fixtures/FileToInspect.php', $path);
        }
    }
}
