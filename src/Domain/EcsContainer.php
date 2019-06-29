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
            $environment = str_replace('.', '_', sprintf(
                    'phpinsights_%s', Kernel::VERSION)
            );

            $easyCodingStandardKernel = new EasyCodingStandardKernel($environment, InputDetector::isDebug());
            $easyCodingStandardKernel->boot();

            $container = $easyCodingStandardKernel->getContainer();

            if ($container === null || ! ($container instanceof Container)) {
                throw new \RuntimeException('Unable to get EcsContainer.');
            }

            self::$container = $container;
        }

        return self::$container;
    }
}
