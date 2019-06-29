<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Symfony;

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

    /**
     * {@inheritDoc}
     */
    public static function get(): array
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

            if (strpos($requirement, 'symfony/framework-bundle') !== false
                || strpos($requirement, 'symfony/flex') !== false
                || strpos($requirement, 'symfony/symfony') !== false) {
                return true;
            }
        }

        return false;
    }
}
