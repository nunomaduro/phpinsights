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
    private $fileList;

    /**
     * @var array<string>
     */
    private $directoryList;

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
            $this->files = $this->getFilesList();
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
                $this->fileList['full_path'][] = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
            } else {
                $this->directoryList[] = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
            }
        }

        $directoryFiles = $this->directoryList === null ? [] : $this->getDirectoryFiles($exclude);
        $singleFiles = $this->fileList === null ? [] : $this->getSingleFiles();

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

        return $this->getFilesList();
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getSingleFiles(): array
    {
        $this->finder = Finder::create()
            ->in($this->fileList['dirname'])
            ->name($this->fileList['basename'])
            ->filter(function (\SplFileInfo $file): bool {
                return \in_array($file->getPathname(), $this->fileList['full_path'], true);
            });

        return $this->getFilesList();
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getFilesList(): array
    {
        return iterator_to_array($this->finder->getIterator(), true);
    }
}
