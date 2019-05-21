<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Domain\Kernel;
use NunoMaduro\PhpInsights\Domain\Reflection;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'insights';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Analyze the code quality';

    /**
     * {@inheritdoc}
     */
    public function handle(AnalyseCommand $analyseCommand): int
    {
        Kernel::bootstrap();

        $configPath = $this->input->getOption('config-path');

        if (is_string($configPath) && !file_exists($configPath)) {
            $this->output->error(
                'First, publish the configuration using: php artisan vendor:publish'
            );

            return 1;
        }

        $output = (new Reflection($this->output))->get('output');

        return $analyseCommand->__invoke($this->input, $output);
    }

    /**
     * {@inheritdoc}
     */
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
