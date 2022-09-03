<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\WordPress;

use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    public static function getName(): string
    {
        return 'wordpress';
    }

    public static function get(Composer $composer): array
    {
        $config = [
            'exclude' => [
                'web/wp',
                'web/.htaccess',
                'web/app/mu-plugins/',
                'web/app/upgrade',
                'web/app/uploads/',
                'web/app/plugins/',
            ],
            'config' => [
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'eval' => null,
                        'error_log' => null,
                        'print_r' => null,
                    ],
                ],
            ],
        ];

        return ConfigResolver::mergeConfig(DefaultPreset::get($composer), $config);
    }

    public static function shouldBeApplied(Composer $composer): bool
    {
        $requirements = $composer->getRequirements();

        foreach (array_keys($requirements) as $requirement) {
            if (strpos($requirement, 'johnpbloch/wordpress') !== false) {
                return true;
            }

            if (strpos($requirement, 'roots/wordpress') !== false) {
                return true;
            }
        }

        return false;
    }
}
