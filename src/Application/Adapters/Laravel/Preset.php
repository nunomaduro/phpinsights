<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    public static function getName(): string
    {
        return 'laravel';
    }

    public static function get(Composer $composer): array
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
                ProtectedToPrivateFixer::class,
                VoidReturnFixer::class,
                StaticClosureSniff::class,
            ],
            'config' => [
                ForbiddenDefineGlobalConstants::class => [
                    'ignore' => ['LARAVEL_START'],
                ],
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'dd' => null,
                        'dump' => null,
                        'ddd' => null,
                        'tinker' => null,
                    ],
                ],
                ForbiddenSetterSniff::class => [
                    'allowedMethodRegex' => [
                        '/^set.*?Attribute$/',
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
            $requirement = (string) $requirement;
            if (strpos($requirement, 'laravel/framework') !== false
                || strpos($requirement, 'illuminate/') !== false) {
                return true;
            }
        }

        return $composer->getName() === 'laravel/framework';
    }
}
