<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Commands\FixCommand;
use NunoMaduro\PhpInsights\Application\Console\Commands\InternalProcessorCommand;
use NunoMaduro\PhpInsights\Application\Console\Commands\InvokableCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Application\Console\Definitions\FixDefinition;
use NunoMaduro\PhpInsights\Application\Console\Definitions\InternalProcessorDefinition;

return (static function (): array {
    $container = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'container.php';

    $analyseCommand = new InvokableCommand(
        'analyse',
        $container->get(AnalyseCommand::class),
        AnalyseDefinition::get()
    );

    $fixCommand = new InvokableCommand(
        'fix',
        $container->get(FixCommand::class),
        FixDefinition::get()
    );

    $internalProcessorCommand = new InvokableCommand(
        InternalProcessorCommand::NAME,
        $container->get(InternalProcessorCommand::class),
        InternalProcessorDefinition::get()
    );
    $internalProcessorCommand->setHidden(true);

    return [
        $analyseCommand,
        $fixCommand,
        $internalProcessorCommand,
    ];
})();
