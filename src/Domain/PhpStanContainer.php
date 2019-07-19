<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Nette\DI\Container;
use PHPStan\DependencyInjection\ContainerFactory;

final class PhpStanContainer
{
    /**
     * @var \Nette\DI\Container
     */
    private static $container;

    public static function make(): Container
    {
        if (self::$container === null) {
            $factory = new ContainerFactory("");

            $container = $factory->create(
                sys_get_temp_dir() . '/phpstan',
                [
                    'vendor/phpstan/phpstan-strict-rules/rules.neon',
                ],
                []
            );

            self::$container = $container;
        }

        return self::$container;
    }
}
