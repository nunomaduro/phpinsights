<?php

namespace NunoMaduro\PhpInsights\Domain;

use PHPStan\DependencyInjection\ContainerFactory;

class PhpStanContainer
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private static $container;

    /**
     * @param $currentWorkingDirectory
     * @return \Nette\DI\Container
     */
    public static function make($currentWorkingDirectory): \Nette\DI\Container
    {
        if (self::$container === null) {
            $factory = new ContainerFactory($currentWorkingDirectory);

            $container = $factory->create(
                sys_get_temp_dir() . '/phpstan',
                [],
                []
            );

            self::$container = $container;
        }

        return self::$container;
    }
}
