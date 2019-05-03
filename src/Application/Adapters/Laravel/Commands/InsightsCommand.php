<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Domain\Kernel;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'insights {directory?} {--config-path=config/insights.php}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Analyze the code quality';

    /**
     * {@inheritdoc}
     */
    public function handle(AnalyseCommand $analyseCommand): void
    {
        Kernel::bootstrap();

        $configPath = $this->input->getOption('config-path');

        if (is_string($configPath) && !file_exists($configPath)) {
            $this->output->note('Consider publish the configuration using the: "php artisan vendor:publish "NunoMaduro\PhpInsights\Application\Adapters\Laravel\InsightsServiceProvider"');
        }

        $analyseCommand->__invoke($this->input, $this->output);
    }
}
