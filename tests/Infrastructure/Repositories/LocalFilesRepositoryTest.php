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
     * @return array<int, array<int, array<int, string>|int>>
     */
    public static function provider(): array
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
            [2, ['/((\w).*)?(FolderA\/)(\w).*/']],
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

        if ($path !== false) {
            self::assertStringContainsString('/Fixtures/FileToInspect.php', $path);
        } else {
            self::fail('Path cannot be false.');
        }
    }

    public function testExcludingSpecificPhpFileDoesNotExcludeFilesContainingThatNameInTheirPath(): void
    {
        $dir = sys_get_temp_dir() . '/phpinsights_test_' . bin2hex(random_bytes(6));
        $observersDir = $dir . '/app/Observers';
        mkdir($observersDir, 0777, true);
        file_put_contents($dir . '/server.php', '<?php // server');
        file_put_contents($observersDir . '/UserObserver.php', '<?php // UserObserver');
        file_put_contents($dir . '/app/User.php', '<?php // User');

        try {
            $finder = new Finder();
            $repository = new LocalFilesRepository($finder);
            $repository->within([$dir], ['server.php']);

            $fileNames = array_map(
                static fn (SplFileInfo $file): string => $file->getFilename(),
                array_values($repository->getFiles())
            );

            sort($fileNames);

            self::assertSame(['User.php', 'UserObserver.php'], $fileNames);
        } finally {
            @unlink($dir . '/server.php');
            @unlink($observersDir . '/UserObserver.php');
            @unlink($dir . '/app/User.php');
            @rmdir($observersDir);
            @rmdir($dir . '/app');
            @rmdir($dir);
        }
    }
}
