<?php

declare(strict_types=1);

use Narration\Console\Router;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$router = new Router(require __DIR__ . '/../container.php');

$router->command('analyse', AnalyseCommand::class, [
    new InputArgument('directory', InputArgument::OPTIONAL),
    new InputOption('config-path', 'c', InputOption::VALUE_OPTIONAL),
]);

$router->setDefault('analyse');

return $router;
