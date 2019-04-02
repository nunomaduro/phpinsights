<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Structure\Composer;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use RuntimeException;

/**
 * @internal
 */
final class ComposerFinder
{
    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     *
     * @return bool
     */
    public static function exists(FilesRepository $filesRepository): bool
    {
        foreach ($filesRepository->getFiles() as $file) {
            if ($file->getFilename() === 'composer.json') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     *
     * @return string
     */
    public static function contents(FilesRepository $filesRepository): string
    {
        foreach ($filesRepository->getFiles() as $file) {
            if ($file->getFilename() === 'composer.json') {
                return $file->getContents();
            }
        }

        throw new RuntimeException('`composer.json` not found.');
    }
}
