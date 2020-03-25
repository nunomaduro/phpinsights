<?php

declare(strict_types=1);

namespace Tests\Fakes;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Finder\SplFileInfo;

final class FakeFileRepository implements FilesRepository
{
    /**
     * @var array<SplFileInfo>
     */
    protected $files = [];

    /**
     * FakeFileRepository constructor.
     *
     * @param array<string> $filePaths
     */
    public function __construct(array $filePaths)
    {
        $this->files = array_map(function (string $filePath) : SplFileInfo {
            return new SplFileInfo($filePath, $filePath, $filePath);
        }, $filePaths);
    }

    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function within(array $dir, array $exclude): FilesRepository
    {
        return $this;
    }
}
