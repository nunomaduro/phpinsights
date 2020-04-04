<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Definitions;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class DefinitionBinder
{
    public static function bind(InputInterface $input): void
    {
        // merge application default definition with current command definition.
        $definition = (new Application())->getDefinition();

        $commandDefinition = BaseDefinition::get();
        if ($input->getFirstArgument() !== 'fix') {
            $commandDefinition = AnalyseDefinition::get();
        }

        $definition->addArguments($commandDefinition->getArguments());
        $definition->addOptions($commandDefinition->getOptions());

        $input->bind($definition);
    }
}
