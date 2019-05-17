<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Commands\InvokableCommand;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;

return (static function () {
    $container = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'container.php';

    $analyseCommand = new InvokableCommand(
        'analyse',
        $container->get(AnalyseCommand::class),
        AnalyseDefinition::get()
    );

    return [
        $analyseCommand,
    ];
})();
