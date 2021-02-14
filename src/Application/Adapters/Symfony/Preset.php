<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Symfony;

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
        return 'symfony';
    }

    public static function get(Composer $composer): array
    {
        $config = [
            'exclude' => [
                'var',
                'translations',
                'config',
                'public',
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

        foreach (array_keys($requirements) as $requirement) {
            if (strpos($requirement, 'symfony/framework-bundle') !== false) {
                return true;
            }
            if (strpos($requirement, 'symfony/flex') !== false) {
                return true;
            }
            if (strpos($requirement, 'symfony/symfony') !== false) {
                return true;
            }
        }

        return false;
    }
}
