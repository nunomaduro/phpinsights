<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FileFactory
{
    private ?Ruleset $ruleset = null;

    private Config $config;

    public function __construct()
    {
        $config = new Config([], false);
        $config->__set('tabWidth', 4);
        $config->__set('annotations', false);
        $config->__set('encoding', 'UTF-8');

        $this->config = $config;
    }

    public function createFromFileInfo(SplFileInfo $smartFileInfo): File
    {
        if ($this->ruleset === null) {
            $this->ruleset = new Ruleset($this->config);
        }

        $path = $smartFileInfo->getRealPath();

        if ($path === false) {
            throw new RuntimeException(
                "{$smartFileInfo->getPath()} Does not exist."
            );
        }

        return new File(
            $path,
            $smartFileInfo->getContents(),
            $this->config,
            $this->ruleset
        );
    }
}
