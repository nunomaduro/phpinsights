<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    public static function getName(): string
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        $config = [
            'exclude' => [
                'config',
                'storage',
                'resources',
                'bootstrap',
                'nova',
                'database',
                'server.php',
                '_ide_helper.php',
                '_ide_helper_models.php',
                'app/Providers/TelescopeServiceProvider.php',
                'public',
            ],
            'add' => [
                // ...
            ],
            'remove' => [
                // ...
            ],
            'config' => [
                ForbiddenDefineGlobalConstants::class => [
                    'ignore' => ['LARAVEL_START'],
                ],
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'dd' => null,
                        'dump' => null,
                    ],
                ],
                ForbiddenSetterSniff::class => [
                    'allowedMethodRegex' => [
                        '/^set.*?Attribute$/',
                    ],
                ],
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
            if (strpos($requirement, 'laravel/framework') !== false
                || strpos($requirement, 'illuminate/') !== false) {
                return true;
            }
        }

        return array_key_exists('name', $composer) && $composer['name'] === 'laravel/framework';
    }
}
