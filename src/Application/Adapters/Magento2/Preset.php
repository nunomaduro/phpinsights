<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Magento2;

use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    private const CONFIG = [
        'exclude' => [
            'bin',
            'dev',
            'generated',
            'lib',
            'phpserver',
            'pub',
            'setup',
            'update',
            'var',
            'app/autoload.php',
            'app/bootstrap.php',
            'app/functions.php',
            'index.php',
        ],
        'add' => [
            // ...
        ],
        'remove' => [
            // ...
        ],
        'config' => [
            // ...
        ],
    ];
    public static function getName(): string
    {
        return 'magento2';
    }

    public static function get(Composer $composer): array
    {
        return ConfigResolver::mergeConfig(DefaultPreset::get($composer), self::CONFIG);
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        $requirements = $composer->getRequirements();

        foreach (array_keys($requirements) as $requirement) {
            if (strpos($requirement, 'magento/magento-cloud-metapackage') !== false) {
                return true;
            }
            if (strpos($requirement, 'magento/product-community-edition') !== false) {
                return true;
            }
            if (strpos($requirement, 'magento/product-enterprise-edition') !== false) {
                return true;
            }
        }

        return false;
    }
}
