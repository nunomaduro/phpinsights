<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    /**
     * {@inheritDoc}
     */
    public static function getName(): string
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'storage',
                'resources',
                'bootstrap',
                'database',
                'server.php',
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
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var string[] $requirements */
        $requirements = $composer['require'];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;
            if (strpos($requirement, 'laravel/framework') !== false
                || strpos($requirement, 'illuminate/') !== false) {
                return true;
            }
        }

        if ($composer['name'] === 'laravel/framework') {
            return true;
        }

        return false;
    }
}
