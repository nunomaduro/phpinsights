<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Finder\Finder;
use Traversable;

/**
 * @internal
 */
final class LocalFilesRepository implements FilesRepository
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder
            ->files()
            ->name(['*.php'])
            ->exclude(['vendor', 'tests', 'Tests', 'test', 'Test'])
            ->notName(['*.blade.php'])
            // ->ignoreVCSIgnored(true)
            ->ignoreUnreadableDirs();
    }

    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    public function getFiles(): Traversable
    {
        return $this->finder->getIterator();
    }

    public function within(string $path, array $exclude = []): FilesRepository
    {
        if (! is_dir($path) && is_file($path)) {
            $pathInfo = pathinfo($path);
            $this->finder = Finder::create()
                ->in($pathInfo['dirname'])
                ->name($pathInfo['basename']);

            return $this;
        }
        $this->finder->in([$path])->notPath($exclude);

        foreach ($exclude as $value) {
            if (substr($value, -4) === '.php') {
                $this->finder->notName($value);
            }
        }

        return $this;
    }
}
