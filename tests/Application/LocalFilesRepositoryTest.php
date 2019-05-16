<?php

namespace Tests\Application;

use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class LocalFilesRepositoryTest extends TestCase
{
    protected $base = __DIR__ . '/../Fixtures/Tree';

    /**
     * @test
     * @dataProvider provider
     * @param int $expected
     * @param array $exclude
     */
    public function it_excludes_files_with_a_path($expected, $exclude)
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
