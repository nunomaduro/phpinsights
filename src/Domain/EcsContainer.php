<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Symfony\Component\DependencyInjection\Container;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\InputDetector;

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
        if (self::$container === null) {
            $easyCodingStandardKernel = new EasyCodingStandardKernel('phpinsights', InputDetector::isDebug());
            $easyCodingStandardKernel->boot();

            self::$container = $easyCodingStandardKernel->getContainer();
        }

        return self::$container;
    }
}
