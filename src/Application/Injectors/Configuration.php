<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
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
                // merge application default definition with analyse definition.
                $definition = (new Application())->getDefinition();
                $analyseDefinition = AnalyseDefinition::get();

                $definition->addArguments($analyseDefinition->getArguments());
                $definition->addOptions($analyseDefinition->getOptions());

                $input->bind($definition);

                $configPath = ConfigResolver::resolvePath($input);
                $config = [];

                if ($configPath !== '' && file_exists($configPath)) {
                    $config = require $configPath;
                }

                return ConfigResolver::resolve($config, $input);
            },
        ];
    }
}
