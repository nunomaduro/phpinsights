<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Drupal\Preset as DrupalPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Magento2\Preset as Magento2Preset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Yii\Preset as YiiPreset;

/**
 * @internal
 */
final class ConfigResolver
{
    /**
     * @var array<string>
     */
    private static $presets = [
        DrupalPreset::class,
        LaravelPreset::class,
        SymfonyPreset::class,
        YiiPreset::class,
        Magento2Preset::class,
        DefaultPreset::class,
    ];

    /**
     * Merge the given config with the specified preset.
     *
     * @param  array<string, string|int|array>  $config
     * @param  string  $directory
     *
     * @return array<string, array>
     */
    public static function resolve(array $config, string $directory): array
    {
        $preset = $config['preset'] ?? self::guess($directory);

        foreach (self::$presets as $presetClass) {
            if ($presetClass::getName() === $preset && is_array($config)) {
                $config = array_replace_recursive($presetClass::get(), $config);
            }
        }

        return is_array($config) ? $config : [];
    }

    /**
     * Guesses the preset based in information from the directory.
     *
     * @param  string  $directory
     *
     * @return string
     */
    public static function guess(string $directory): string
    {
        $preset = 'default';

        $composerPath = $directory . DIRECTORY_SEPARATOR . 'composer.json';

        if (! file_exists($composerPath)) {
            return $preset;
        }

        $composer = json_decode((string) file_get_contents($composerPath), true);

        foreach (self::$presets as $presetClass) {
            if ($presetClass::shouldBeApplied($composer)) {
                $preset = $presetClass::getName();
                break;
            }
        }

        return $preset;
    }
}
