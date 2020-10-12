<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Drupal;

use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\GuessablePreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * @internal
 */
final class Preset implements PresetContract, GuessablePreset
{
    public static function getName(): string
    {
        return 'drupal';
    }

    public static function get(Composer $composer): array
    {
        $config = [
            'exclude' => [
                'core',
                'modules/contrib',
                'sites',
                'profiles/contrib',
                'themes/contrib',
            ],
            'config' => [
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'dd' => null,
                        'dump' => null,
                    ],
                ],
            ],
        ];

        return ConfigResolver::mergeConfig(DefaultPreset::get($composer), $config);
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        $requirements = $composer->getRequirements();
        $replace = $composer->getReplacements();

        foreach (array_keys(array_merge($requirements, $replace)) as $requirement) {
            if (strpos($requirement, 'drupal/core') !== false) {
                return true;
            }
        }

        return false;
    }
}
