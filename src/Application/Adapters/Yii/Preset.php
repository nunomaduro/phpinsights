<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Yii;

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
        return 'yii';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        $config = [
            'exclude' => [
                'web',
                'views',
                'vagrant',
                'runtime',
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
    public static function shouldBeApplied(array $composer, string $directory): bool
    {
        /** @var array<string> $requirements */
        $requirements = $composer['require'] ?? [];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;
            if (strpos($requirement, 'yiisoft/yii2') !== false) {
                return true;
            }
        }

        return false;
    }
}
