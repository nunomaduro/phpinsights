<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FileFactory
{
    public function createFromFileInfo(SplFileInfo $smartFileInfo): File
    {
        return new File(
            $smartFileInfo->getRelativePathname(),
            $smartFileInfo->getContents()
        );
    }
}
