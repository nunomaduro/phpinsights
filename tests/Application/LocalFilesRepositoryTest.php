<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * @covers \NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository
 */
final class LocalFilesRepositoryTest extends TestCase
{
    protected $base = __DIR__ . '/../Fixtures/Tree';

    /**
     * @test
     * @dataProvider provider
     * @param int $expected
     * @param array $exclude
     */
    public function itExcludesFilesGivenAPath(int $expected, array $exclude): void
    {
        $repository = new LocalFilesRepository(Finder::create());

        $files = $repository->within($this->base, $exclude)->getFiles();

        $this->assertCount($expected, $files);
    }

    public function provider()
    {
        return [
            [3, ['FolderA/ClassA.php']],
            [4, []],
            [2, ['ClassA.php']],
            [2, ['FolderA']],
            [1, ['FolderA', 'FolderB/ClassB.php']],
            [3, ['FolderA/SubFolderA']],
            [3, ['FolderA/SubFolderA/ClassC.php']],
        ];
    }

}
