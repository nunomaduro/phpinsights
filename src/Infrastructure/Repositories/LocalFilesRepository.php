<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class LocalFilesRepository implements FilesRepository
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * @var array<\Symfony\Component\Finder\SplFileInfo>
     */
    private $files;

    /**
     * @var array<mixed>
     */
    private $fileList = [];

    /**
     * @var array<string>
     */
    private $directoryList = [];

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    public function getFiles(): array
    {
        if ($this->files === null) {
            $this->files = $this->getIterator();
        }

        return $this->files;
    }

    public function within(array $paths, array $exclude = []): FilesRepository
    {
        foreach ($paths as $path) {
            $pathInfo = pathinfo($path);

            if (! is_dir($path) && is_file($path)) {
                $this->fileList['dirname'][] = $pathInfo['dirname'];
                $this->fileList['basename'][] = $pathInfo['basename'];
            } else {
                $this->directoryList[] = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
            }
        }

        $directoryFiles = [];
        $singleFiles = [];

        if ($this->directoryList !== []) {
            $directoryFiles = $this->getDirectoryFiles($exclude);
        }

        if ($this->fileList !== []) {
            $singleFiles = $this->getSingleFiles();
        }

        $this->files = array_merge($directoryFiles, $singleFiles);

        return $this;
    }

    /**
     * @param array<string> $exclude
     *
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getDirectoryFiles(array $exclude = []): array
    {
        $this->finder = Finder::create()
            ->files()
            ->name(['*.php'])
            ->exclude(['vendor', 'tests', 'Tests', 'test', 'Test'])
            ->notName(['*.blade.php'])
            ->ignoreUnreadableDirs()
            ->in($this->directoryList)
            ->notPath($exclude);

        foreach ($exclude as $value) {
            if (substr($value, -4) === '.php') {
                $this->finder->notName($value);
            }
        }

        return $this->getIterator();
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getSingleFiles(): array
    {
        $this->finder = Finder::create()
            ->in($this->fileList['dirname'])
            ->name($this->fileList['basename']);

        return $this->getIterator();
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getIterator(): array
    {
        return iterator_to_array($this->finder->getIterator(), true);
    }
}
