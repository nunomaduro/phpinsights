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


    public static function make(): ContainerInterface
    {
        if (self::$container === null) {
            $easyCodingStandardKernel = new EasyCodingStandardKernel('phpinsights', InputDetector::isDebug());
            $easyCodingStandardKernel->boot();

            if ($easyCodingStandardKernel->getContainer() === null) {
                throw new \RuntimeException('Unable to get EcsContainer');
            }

            self::$container = $easyCodingStandardKernel->getContainer();
        }

        return self::$container;
    }
}
