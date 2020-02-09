<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Application\DirectoryResolver;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Kernel;
use NunoMaduro\PhpInsights\Domain\Reflection;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    /** @var string */
    protected $name = 'insights';

    /** @var string */
    protected $description = 'Analyze the code quality';

    public function handle(): int
    {
        Kernel::bootstrap();

        $configPath = ConfigResolver::resolvePath($this->input);

        if (! file_exists($configPath)) {
            $this->output->error('First, publish the configuration using: php artisan vendor:publish');
            return 1;
        }

        $configuration = require $configPath;
        $configuration = ConfigResolver::resolve($configuration, DirectoryResolver::resolve($this->input));

        $container = Container::make();
        if (! $container instanceof \League\Container\Container) {
            throw new \RuntimeException('Container should be League Container instance');
        }

        $configurationDefinition = $container->extend(Configuration::class);
        $configurationDefinition->setConcrete($configuration);

        $analyseCommand = $container->get(AnalyseCommand::class);

        $output = (new Reflection($this->output))->get('output');
        return $analyseCommand->__invoke($this->input, $output);
    }

    public function configure(): void
    {
        parent::configure();

        $this->setDefinition(
            AnalyseDefinition::get()
        );

        $this->getDefinition()
            ->getOption('config-path')
            ->setDefault('config/insights.php');
    }
}
