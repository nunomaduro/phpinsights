<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Yii;

use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\GuessablePreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;

/**
 * @internal
 */
final class Preset implements PresetContract, GuessablePreset
{
    private const CONFIG = [
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
    public static function getName(): string
    {
        return 'yii';
    }

    public static function get(Composer $composer): array
    {
        return ConfigResolver::mergeConfig(DefaultPreset::get($composer), self::CONFIG);
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        $requirements = $composer->getRequirements();

        foreach (array_keys($requirements) as $requirement) {
            if (strpos($requirement, 'yiisoft/yii2') !== false) {
                return true;
            }
        }

        return false;
    }
}
