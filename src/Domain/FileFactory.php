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
    /** @var \PHP_CodeSniffer\Ruleset */
    private $ruleset;

    /** @var \PHP_CodeSniffer\Config */
    private $config;

    public function __construct()
    {
        $config = new Config([], false);
        $config->__set('tabWidth', 4);
        $config->__set('annotations', false);
        $config->__set('encoding', 'UTF-8');

        $this->config = $config;
        $this->ruleset = new Ruleset($config);
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
