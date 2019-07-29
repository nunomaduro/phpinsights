<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Drupal;

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
        return 'drupal';
    }

    public static function get(): array
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

        return ConfigResolver::mergeConfig(DefaultPreset::get(), $config);
    }

    public static function shouldBeApplied(array $composer): bool
    {
        /** @var array<string, string> $requirements */
        $requirements = $composer['require'] ?? [];

        /** @var array<string, string> $replace */
        $replace = $composer['replace'] ?? [];

        foreach (array_keys(array_merge($requirements, $replace)) as $requirement) {
            if (strpos($requirement, 'drupal/core') !== false) {
                return true;
            }
        }

        return false;
    }
}
