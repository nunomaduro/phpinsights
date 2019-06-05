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
    /**
     * @var string
     */
    private $base = __DIR__ . '/../Fixtures/Tree';

    /**
     * @test
     * @dataProvider provider
     * @param int $expected
     * @param array<string> $exclude
     */
    public function itExcludesFilesGivenAPath(int $expected, array $exclude): void
    {
        $repository = new LocalFilesRepository(Finder::create());

        $files = $repository->within($this->base, $exclude)->getFiles();

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
}
