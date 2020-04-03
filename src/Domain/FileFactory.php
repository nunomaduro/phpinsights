<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FileFactory
{
    public function createFromFileInfo(SplFileInfo $smartFileInfo): File
    {
        $path = $smartFileInfo->getRealPath();

        if ($path === false) {
            throw new RuntimeException(
                "{$smartFileInfo->getPath()} Does not exist."
            );
        }

        return new File(
            $path,
            $smartFileInfo->getContents()
        );
    }
}
