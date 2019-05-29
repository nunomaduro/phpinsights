<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\InputDetector;

/**
 * @internal
 */
final class EcsContainer
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private static $container;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function make(): ContainerInterface
    {
        if (null === self::$container) {
            $environment = str_replace('.', '_', sprintf(
                    'phpinsights_%s', Kernel::VERSION)
            );

            $easyCodingStandardKernel = new EasyCodingStandardKernel($environment,
                InputDetector::isDebug());
            $easyCodingStandardKernel->boot();

            if (null === $easyCodingStandardKernel->getContainer()) {
                throw new \RuntimeException('Unable to get EcsContainer.');
            }

            self::$container = $easyCodingStandardKernel->getContainer();
        }

        return self::$container;
    }
}
