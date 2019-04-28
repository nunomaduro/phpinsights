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

        $analyseCommand->__invoke($this->input, $this->output);
    }
}
