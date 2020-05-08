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
     */
    public function getDefaultDirectory(): string;

    /**
     * Get the files.
     *
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    public function getFiles(): array;

    /**
     * Sets the current files paths.
     *
     * @param array<string> $paths
     * @param array<string> $exclude
     */
    public function within(array $paths, array $exclude): FilesRepository;
}
