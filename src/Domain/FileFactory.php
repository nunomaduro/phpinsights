<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FileFactory
{
    /** @var \PHP_CodeSniffer\Ruleset */
    private $ruleset;

    /** @var \PHP_CodeSniffer\Config */
    private $config;

    public function __construct(Ruleset $ruleset, Config $config)
    {
        $this->ruleset = $ruleset;
        $this->config = $config;
    }

    public function createFromFileInfo(SplFileInfo $smartFileInfo): File
    {
        return new File(
            $this->ruleset,
            $this->config,
            $smartFileInfo->getRelativePathname(),
            $smartFileInfo->getContents()
        );
    }
}
