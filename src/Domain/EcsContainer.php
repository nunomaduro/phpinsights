<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Symfony\Component\DependencyInjection\Container;

/**
 * @internal
 */
final class EcsContainer
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private static $container;

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public static function make(): Container
    {
        if (null === self::$container) {
            if (file_exists(__DIR__.'/../../vendor/symplify/easy-coding-standard/bin/container.php')) {
                $containerPath = __DIR__.'/../../vendor/symplify/easy-coding-standard/bin/container.php';
            } else {
                $containerPath = __DIR__.'/../../../../symplify/easy-coding-standard/bin/container.php';
            }

            self::$container = require $containerPath;
        }

        return self::$container;
    }
}
