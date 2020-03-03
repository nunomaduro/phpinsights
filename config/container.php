<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use NunoMaduro\PhpInsights\Application\Providers\ConfigurationProvider;
use NunoMaduro\PhpInsights\Application\Providers\FileProcessorsProvider;
use NunoMaduro\PhpInsights\Application\Providers\InsightLoadersProvider;
use NunoMaduro\PhpInsights\Application\Providers\IOProvider;
use NunoMaduro\PhpInsights\Application\Providers\RepositoriesProvider;

return (static function (): Container {
    $serviceProviders = [
        ConfigurationProvider::class,
        RepositoriesProvider::class,
        InsightLoadersProvider::class,
        FileProcessorsProvider::class,
        IOProvider::class,
    ];

    $container = (new Container());

    foreach ($serviceProviders as $provider) {
        $container->addServiceProvider($provider);
    }

    $container->delegate(new ReflectionContainer());

    return $container;
})();
