<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class Container
{
    private static ?ContainerInterface $container = null;

    public static function make(): ContainerInterface
    {
        if (self::$container === null) {
            self::$container = require __DIR__ . '/../../config/container.php';
        }

        return self::$container;
    }
}
