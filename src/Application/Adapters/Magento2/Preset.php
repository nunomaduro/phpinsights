<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Magento2;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    public static function getName(): string
    {
        return 'magento2';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        $config = [
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

        return ConfigResolver::mergeConfig(DefaultPreset::get(), $config);
    }

    /**
     * {@inheritDoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var array<string> $requirements */
        $requirements = $composer['require'] ?? [];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;

            if (strpos($requirement, 'magento/magento-cloud-metapackage') !== false
                || strpos($requirement, 'magento/product-community-edition') !== false
                || strpos($requirement, 'magento/product-enterprise-edition') !== false) {
                return true;
            }
        }

        return false;
    }
}
