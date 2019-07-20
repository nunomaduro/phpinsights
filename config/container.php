<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use NunoMaduro\PhpInsights\Application\Injectors\FileProcessors;
use NunoMaduro\PhpInsights\Application\Injectors\Repositories;

return (static function () {
    $injectors = [
        Repositories::class,
        FileProcessors::class,
    ];

    $container = (new Container())->delegate(new ReflectionContainer());

    foreach ($injectors as $injector) {
        foreach ((new $injector())() as $id => $concrete) {
            $container->add($id, $concrete);
        }
    }

    return $container;
})();
