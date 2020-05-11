<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Nette\Utils\FileSystem;
use Psr\Container\ContainerInterface;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Symfony\Component\Yaml\Yaml;

final class RectorContainer
{
    private static ContainerInterface $container;

    /**
     * @param array<string> $rectorClasses
     */
    public static function make(array $rectorClasses): ContainerInterface
    {
        return self::$container ??= (new RectorContainerFactory())
            ->createFromConfigs([self::createConfig($rectorClasses)]);
    }

    public static function get(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * @param array<string> $rectorClasses
     */
    private static function createConfig(array $rectorClasses): string
    {
        $listForConfig = [];
        foreach ($rectorClasses as $rectorClass) {
            $listForConfig[$rectorClass] = [
                'public' => true,
            ];
        }

        $yamlContent = Yaml::dump([
            'services' => $listForConfig,
        ], Yaml::DUMP_OBJECT_AS_MAP);

        $configFileTempPath = sprintf(sys_get_temp_dir() . '/rector_config.yaml');

        FileSystem::write($configFileTempPath, $yamlContent);

        return $configFileTempPath;
    }
}
