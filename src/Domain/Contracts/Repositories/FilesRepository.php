<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts\Repositories;

/**
 * @internal
 */
interface FilesRepository
{
    /**
     * Get the files.
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles(): iterable;

    /**
     * Sets the current files dirs.
     *
     * @param  string[]  $dirs
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    public function in(array $dirs): FilesRepository;
}
