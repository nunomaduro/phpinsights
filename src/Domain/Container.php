<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class Container
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private static $container;

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public static function make(): ContainerInterface
    {
        if (null === self::$container) {
            self::$container = require __DIR__.'/../../config/container.php';
        }

        return self::$container;
    }
}
