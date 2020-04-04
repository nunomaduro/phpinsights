<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Minimum definition for each of our commands.
 *
 * @internal
 */
abstract class BaseDefinition
{
    public static function get(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument(
                'paths',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Paths of directories or files to analyse'
            ),
            new InputOption(
                'config-path',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The configuration file path'
            ),
        ]);
    }
}
