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
        new InputOption(
            'min-quality',
            null,
            InputOption::VALUE_OPTIONAL,
            'Minimal Quality level to reach without throw error',
            0
        ),
        new InputOption(
            'min-complexity',
            null,
            InputOption::VALUE_OPTIONAL,
            'Minimal Complexity level to reach without throw error',
            0
        ),
        new InputOption(
            'min-architecture',
            null,
            InputOption::VALUE_OPTIONAL,
            'Minimal Architecture level to reach without throw error',
            0
        ),
        new InputOption(
            'min-style',
            null,
            InputOption::VALUE_OPTIONAL,
            'Minimal Style level to reach without throw error',
            0
        ),
    ]);

    return [
        $analyseCommand,
    ];
})();
