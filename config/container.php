<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use NunoMaduro\PhpInsights\Application\Providers\ConfigurationProvider;
use NunoMaduro\PhpInsights\Application\Providers\FileProcessorsProvider;
use NunoMaduro\PhpInsights\Application\Providers\InsightLoadersProvider;
use NunoMaduro\PhpInsights\Application\Providers\RepositoriesProvider;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\PhpStanContainer;
use PHPStan\DependencyInjection\ContainerFactory;

return (static function (): Container {
    $serviceProviders = [
        ConfigurationProvider::class,
        RepositoriesProvider::class,
        InsightLoadersProvider::class,
        FileProcessorsProvider::class,
    ];

    $container = (new Container());

    foreach ($serviceProviders as $provider) {
        $container->addServiceProvider($provider);
    }

    $container->delegate($phpStan = new PhpStanContainer(
        (new ContainerFactory($container->get(Configuration::class)->getDirectory()))->create(
            sys_get_temp_dir() . '/phpstan',
            [
                file_exists(__DIR__ . '/../vendor/phpstan/phpstan-strict-rules/rules.neon')
                    ? __DIR__ . '/../vendor/phpstan/phpstan-strict-rules/rules.neon'
                    : __DIR__ . '/../../../../vendor/phpstan/phpstan-strict-rules/rules.neon',
            ],
            []
        )
    ));
    $container->add(PhpStanContainer::class, $phpStan);

    $container->delegate(new ReflectionContainer());

    return $container;
})();
