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
    private Ruleset $ruleset;

    private Config $config;

    public function __construct()
    {
        $config = new Config([], false);
        // disable loading custom ruleset
        $config->restoreDefaults();
        $config->__set('tabWidth', 4);
        $config->__set('annotations', false);
        $config->__set('encoding', 'UTF-8');
        // Include only 1 sniff, they are register later
        $config->__set('sniffs', ['Generic.Files.LineEndings']);

        $this->config = $config;
        $this->ruleset = new Ruleset($this->config);
    }

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
            $smartFileInfo->getContents(),
            $this->config,
            $this->ruleset
        );
    }
}
