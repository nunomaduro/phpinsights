<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Symfony;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;

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
        return 'symfony';
    }

    /**
     * {@inheritdoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'var',
                'translations',
                'config',
                'public',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var string[] $requirements */
        $requirements = $composer['require'];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;

            if (false !== strpos($requirement, 'symfony/framework-bundle')
                || false !== strpos($requirement, 'symfony/flex')
                || false !== strpos($requirement, 'symfony/symfony')) {
                return true;
            }
        }

        return false;
    }
}
