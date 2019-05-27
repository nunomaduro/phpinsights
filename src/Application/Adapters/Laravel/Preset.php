<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'laravel';
    }

    /**
     * {@inheritdoc}
     */
    public static function get(): array
    {
        return [
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
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var string[] $requirements */
        $requirements = $composer['require'] ?? [];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;
            if (false !== strpos($requirement, 'laravel/framework')
                || false !== strpos($requirement, 'illuminate/')) {
                return true;
            }
        }

        return array_key_exists('name', $composer) && 'laravel/framework' === $composer['name'];
    }
}
