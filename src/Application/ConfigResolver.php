<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;

/**
 * @internal
 */
final class ConfigResolver
{
    /**
     * Merge the given config with the specified preset.
     *
     * @param  array<string, string|int|array>  $config
     * @param  string  $directory
     *
     * @return array
     */
    public static function resolve(array $config, string $directory): array
    {
        $preset = $config['preset'] ?? self::guess($directory);

        switch ($preset) {
            case 'laravel':
                $config = array_merge_recursive(LaravelPreset::get(), $config);
                break;

            case 'symfony':
                $config = array_merge_recursive(SymfonyPreset::get(), $config);
                break;
        }

        return $config;
    }

    /**
     * Guesses the preset based in information from the directory.
     *
     * @param  string  $directory
     *
     * @return string
     */
    public static function guess(string $directory): ?string
    {
        $preset = null;

        if (! file_exists($composerPath = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
            return $preset;
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        foreach ($composer['require'] as $requirement => $version) {
            if (strpos($requirement, 'laravel/framework') !== false || strpos($requirement, 'illuminate/') !== false) {
                $preset = 'laravel';
                break;
            }

            if (strpos($requirement, 'symfony/framework-bundle') !== false || strpos($requirement, 'symfony/flex') !== false) {
                $preset = 'symfony';
                break;
            }
        }

        return $preset;
    }
}
