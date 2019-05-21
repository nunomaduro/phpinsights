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
     * LocalFilesRepository constructor.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder
            ->files()
            ->name(['*.php'])
            ->exclude(['vendor', 'tests', 'Tests', 'test', 'Test'])
            // ->ignoreVCSIgnored(true)
            ->ignoreUnreadableDirs();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDirectory(): string
    {
        return (string)getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): iterable
    {
        return $this->finder->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function within(string $directory, array $exclude): FilesRepository
    {
        $this->finder->in([$directory])->exclude($exclude);

        foreach ($exclude as $value) {
            if (substr($value, -4) === '.php') {
                $this->finder->notName($value);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $source)
    {
        return $this->getFiles();
    }
}
