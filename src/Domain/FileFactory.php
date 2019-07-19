<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Fixer;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FileFactory
{
    /**
     * @var \PHP_CodeSniffer\Fixer
     */
    private $fixer;

    public function __construct()
    {
        $this->fixer = new Fixer();
    }

    public function createFromFileInfo(SplFileInfo $smartFileInfo): File
    {
        return new File(
            $smartFileInfo->getRelativePathname(),
            $smartFileInfo->getContents(),
            $this->fixer
        );
    }
}
