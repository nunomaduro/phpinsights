<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @see \Tests\Infrastructure\Repositories\LocalFilesRepositoryTest
 */
final class LocalFilesRepository implements FilesRepository
{
    private Finder $finder;

    /**
     * @var array<\Symfony\Component\Finder\SplFileInfo>
     */
    private array $files = [];

    /**
     * @var array<string>
     */
    private array $paths = [];

    /**
     * @var array<string>
     */
    private array $exclude = [];

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    /**
     * @return array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    public function getFiles(): array
    {
        if (count($this->files) !== 0) {
            return $this->files;
        }

        $withPathInfo = static function (string $path): array {
            return [
                'dirname' => $dirname = pathinfo($path, PATHINFO_DIRNAME),
                'basename' => $basename = pathinfo($path, PATHINFO_BASENAME),
                'full_path' => $dirname . DIRECTORY_SEPARATOR . $basename,
                'is_file' => ! is_dir($path) && is_file($path),
            ];
        };

        $paths = array_map($withPathInfo, $this->paths);

        $directories = array_filter($paths, fn (array $path): bool => ! $path['is_file']);
        $files = array_filter($paths, fn (array $path): bool => $path['is_file']);

        return $this->files = array_merge(
            $this->filesWithinDirectories($directories, $this->exclude),
            $this->filesAtPaths($files)
        );
    }

    public function within(array $paths, array $exclude = []): FilesRepository
    {
        $this->paths = $paths;
        $this->exclude = $exclude;

        return $this;
    }

    /**
     * @param array<string> $directories
     * @param array<string> $exclude
     *
     * @return array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private function filesWithinDirectories(array $directories, array $exclude = []): array
    {
        if (count($directories) === 0) {
            return [];
        }

        $directories = array_column($directories, 'full_path');

        $finder = (clone $this->finder)
            ->files()
            ->name(['*.php'])
            ->exclude(['vendor', 'tests', 'Tests', 'test', 'Test'])
            ->notName(['*.blade.php'])
            ->ignoreUnreadableDirs()
            ->in($directories)
            ->notPath($exclude);

        foreach ($exclude as $value) {
            if (substr($value, -4) === '.php') {
                $finder->notName($value);
            }
        }

        return $this->getFilesList($finder);
    }

    /**
     * @param array<string> $paths
     *
     * @return array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private function filesAtPaths(array $paths): array
    {
        if (count($paths) === 0) {
            return [];
        }

        $dirname = array_column($paths, 'dirname');
        $basename = array_column($paths, 'basename');
        $full_path = array_column($paths, 'full_path');

        $finder = (clone $this->finder)
            ->in($dirname)
            ->name($basename)
            ->filter(fn (SplFileInfo $file): bool => in_array(
                $file->getPathname(),
                $full_path,
                true
            ));

        return $this->getFilesList($finder);
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    private function getFilesList(Finder $finder): array
    {
        return iterator_to_array($finder->getIterator(), true);
    }
}
