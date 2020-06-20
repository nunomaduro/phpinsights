<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

final class InternalProcessorDefinition
{
    public static function get(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument(
                'files',
                InputArgument::IS_ARRAY,
                'Paths of directories or files to analyse'
            ),
        ]);
    }
}
