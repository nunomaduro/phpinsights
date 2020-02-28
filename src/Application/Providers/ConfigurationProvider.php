<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Application\DirectoryResolver;
use NunoMaduro\PhpInsights\Domain\Configuration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @internal
 */
final class ConfigurationProvider extends AbstractServiceProvider
{
    /** @var array<class-string> */
    protected $provides = [
        Configuration::class,
    ];

    public function register()
    {
        $this->getLeagueContainer()->add(
            Configuration::class,
            static function (): Configuration {
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

                return ConfigResolver::resolve(
                    $config,
                    DirectoryResolver::resolve($input)
                );
            }
        );
    }
}
