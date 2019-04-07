<?php

declare(strict_types=1);

use Narration\Console\Router;
use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use Symfony\Component\Console\Input\InputArgument;

$router = new Router(require __DIR__ . '/../container.php');

$router->command('analyse', AnalyseCommand::class, [
    new InputArgument('directory', InputArgument::OPTIONAL),
    new InputArgument('config-path', InputArgument::OPTIONAL),
]);

$router->setDefault('analyse');

return $router;
