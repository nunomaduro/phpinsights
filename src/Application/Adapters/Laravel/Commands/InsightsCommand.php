<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;

/**
 * @internal
 */
final class InsightsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'insights {directory?} {--config-path=config/insights.php} {--fail-under=100.0}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Analyze the code quality';

    /**
     * {@inheritdoc}
     */
    public function handle(AnalyseCommand $analyseCommand): void
    {
        /**
         * Includes PHP Codesniffer's autoload.
         */
        include_once 'vendor/squizlabs/php_codesniffer/autoload.php';

        $analyseCommand->__invoke($this->input, $this->output);
    }
}
