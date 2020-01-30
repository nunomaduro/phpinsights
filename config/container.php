<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use NunoMaduro\PhpInsights\Application\Injectors\Configuration;
use NunoMaduro\PhpInsights\Application\Injectors\FileProcessors;
use NunoMaduro\PhpInsights\Application\Injectors\InsightLoaders;
use NunoMaduro\PhpInsights\Application\Injectors\Repositories;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;

return (static function (): Container {
    $injectors = [
        Configuration::class,
        Repositories::class,
        FileProcessors::class,
        InsightLoaders::class,
    ];

    $tagsDefinition = [
        FileProcessors::class => FileProcessor::FILE_PROCESSOR_TAG,
        InsightLoaders::class => InsightLoader::INSIGHT_LOADER_TAG,
    ];

    $container = (new Container())->delegate(new ReflectionContainer());

    foreach ($injectors as $injector) {
        foreach ((new $injector())() as $id => $concrete) {
            $definition = $container->add($id, $concrete);

            if (isset($tagsDefinition[$injector])) {
                $definition->addTag($tagsDefinition[$injector]);
            }
        }
    }

    return $container;
})();
