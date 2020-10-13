<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * @internal
 */
final class InternalProcessorDefinition
{
    public static function get(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument(
                'cache-key',
                InputArgument::REQUIRED,
                'Cache key containing files to analyse'
            ),
        ]);
    }
}
