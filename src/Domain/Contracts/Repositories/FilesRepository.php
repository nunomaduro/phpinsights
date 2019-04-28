<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts\Repositories;

/**
 * @internal
 */
interface FilesRepository
{
    /**
     * Get the default repository.
     *
     * @return string
     */
    public function getDefaultDirectory(): string;

    /**
     * Get the files.
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles(): iterable;

    /**
     * Sets the current files dirs.
     *
     * @param  string  $dir
     * @param  string[]  $exclude
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    public function within(string $dir, array $exclude): FilesRepository;
}
