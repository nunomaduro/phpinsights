<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;

/**
 * @internal
 */
final class ConfigResolver
{
    /**
     * Merge the given config with the specified preset.
     *
     * @param  array<string, string|int|array>  $config
     *
     * @return array
     */
    public static function resolve(array $config): array
    {
        switch ($config['preset'] ?? '') {
            case 'laravel':
                $config = array_merge_recursive(LaravelPreset::get(), $config);
                break;
        }

        return $config;
    }
}
