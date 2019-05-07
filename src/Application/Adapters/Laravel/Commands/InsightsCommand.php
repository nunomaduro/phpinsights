<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
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
    protected $signature = 'insights {directory?} {--config-path=config/insights.php}';

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

        if (is_string($configPath) && ! file_exists($configPath)) {
            $this->output->error('First, publish the configuration using: php artisan vendor:publish');
            return 1;
        }

        $output = (new Reflection($this->output))->get('output');

        $analyseCommand->__invoke($this->input, $output);

        return 0;
    }
}
