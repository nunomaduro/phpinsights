<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use NunoMaduro\PhpInsights\Application\Console\Commands\InvokableCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

return (static function () {

    $container = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'container.php';

    $analyseCommand = new InvokableCommand('analyse', $container->get(AnalyseCommand::class), [
        new InputArgument('directory', InputArgument::OPTIONAL),
        new InputOption('config-path', 'c', InputOption::VALUE_OPTIONAL),
    ]);

    return [
        $analyseCommand,
    ];
})();
