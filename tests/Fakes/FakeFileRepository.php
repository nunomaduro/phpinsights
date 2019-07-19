<?php

declare(strict_types=1);

namespace Tests\Fakes;

use ArrayIterator;
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

    /**
     * {@inheritdoc}
     */
    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): iterable
    {
        return new ArrayIterator($this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function within(string $dir, array $exclude): FilesRepository
    {
        return $this;
    }
}
