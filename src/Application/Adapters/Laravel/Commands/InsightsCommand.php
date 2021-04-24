<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Kernel;
use NunoMaduro\PhpInsights\Domain\Reflection;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    protected $name = 'insights';

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
        /**
         * @noRector Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector
         */
        $configuration['fix'] = $this->hasOption('fix') && (bool) $this->option('fix') === true;
        $configuration = ConfigResolver::resolve($configuration, $this->input);

        $container = Container::make();
        if (! $container instanceof \League\Container\Container) {
            throw new RuntimeException('Container should be League Container instance');
        }

        $configurationDefinition = $container->extend(Configuration::class);
        $configurationDefinition->setConcrete($configuration);

        $analyseCommand = $container->get(AnalyseCommand::class);

        $output = (new Reflection($this->output))->get('output');

        $result = $analyseCommand->__invoke($this->input, $output);

        if ($output instanceof ConsoleOutputInterface) {
            $output->getErrorOutput()->writeln('✨ See something that needs to be improved? <options=bold>Create an issue</> or send us a <options=bold>pull request</>: <fg=cyan;options=bold>https://github.com/nunomaduro/phpinsights</>');
        }

        return $result;
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
