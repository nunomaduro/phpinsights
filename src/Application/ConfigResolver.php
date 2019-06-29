<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Drupal\Preset as DrupalPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Magento2\Preset as Magento2Preset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Yii\Preset as YiiPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset;
use NunoMaduro\PhpInsights\Domain\Exceptions\PresetNotFound;

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
        /** @var string $preset */
        $preset = $config['preset'] ?? self::guess($directory);

        /** @var Preset $presetClass */
        foreach (self::$presets as $presetClass) {
            if ($presetClass::getName() === $preset) {
                return self::mergeConfig($presetClass::get(), $config);
            }
        }

        throw new PresetNotFound(sprintf('%s not found', $preset));
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

    /**
     * @see https://www.php.net/manual/en/function.array-merge-recursive.php#96201
     *
     * @param mixed[] $base
     * @param mixed[] $replacement
     *
     * @return array<string, array>
     */
    public static function mergeConfig(array $base, array $replacement): array
    {
        foreach ($replacement as $key => $value) {
            if (! array_key_exists($key, $base) && ! is_numeric($key)) {
                $base[$key] = $replacement[$key];
                continue;
            }
            if (is_array($value) || (array_key_exists($key, $base) && is_array($base[$key]))) {
                $base[$key] = self::mergeConfig($base[$key], $replacement[$key]);
            } elseif (is_numeric($key)) {
                if (! in_array($value, $base, true)) {
                    $base[] = $value;
                }
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
