<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Application\Console\Definitions\DefaultDefinition;
use NunoMaduro\PhpInsights\Application\DirectoryResolver;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @internal
 */
final class Configuration
{
    /**
     * Inject Configuration resolved into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            \NunoMaduro\PhpInsights\Domain\Configuration::class => static function (): \NunoMaduro\PhpInsights\Domain\Configuration {
                $input = new ArgvInput();
                // merge application default definition with current command definition.
                $definition = (new Application())->getDefinition();

                // TODO make a DefinitionResolver
                $commandDefinition = DefaultDefinition::get();
                if ($input->getFirstArgument() !== 'fix') {
                    $commandDefinition = AnalyseDefinition::get();
                }

                $definition->addArguments($commandDefinition->getArguments());
                $definition->addOptions($commandDefinition->getOptions());

                $input->bind($definition);

                $configPath = ConfigResolver::resolvePath($input);
                $config = [];

                if ($configPath !== '' && file_exists($configPath)) {
                    $config = require $configPath;
                }

                $fixOption = $input->hasOption('fix') && (bool) $input->getOption('fix') === true;

                $config['fix'] = $fixOption || $input->getFirstArgument() === 'fix';

                return ConfigResolver::resolve($config, DirectoryResolver::resolve($input));
            },
        ];
    }
}
