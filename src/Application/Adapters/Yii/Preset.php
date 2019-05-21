<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Yii;

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
        return 'yii';
    }

    /**
     * {@inheritdoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'web',
                'views',
                'vagrant',
                'runtime',
            ],
            'add' => [
                // ...
            ],
            'remove' => [
                // ...
            ],
            'config' => [
                // ...
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
            if (strpos($requirement, 'yiisoft/yii2') !== false) {
                return true;
            }
        }

        return false;
    }
}
