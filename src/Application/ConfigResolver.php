<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use NunoMaduro\PhpInsights\Application\Adapters\Drupal\Preset as DrupalPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Magento2\Preset as Magento2Preset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Yii\Preset as YiiPreset;
use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\Preset;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidPresetException;
use NunoMaduro\PhpInsights\Domain\Kernel;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 *
 * @see \Tests\Application\ConfigResolverTest
 */
final class ConfigResolver
{
    private const CONFIG_FILENAME = 'phpinsights.php';

    private const COMPOSER_FILENAME = 'composer.json';

    private const DEFAULT_PRESET = DefaultPreset::class;

    private const PRESETS = [
        DrupalPreset::class,
        LaravelPreset::class,
        SymfonyPreset::class,
        YiiPreset::class,
        Magento2Preset::class,
    ];

    /**
     * Merge the given config with the specified preset.
     *
     * @param array<string, string|array> $config
     *
     * @return \NunoMaduro\PhpInsights\Domain\Configuration
     *
     * @throws \JsonException
     */
    public static function resolve(array $config, InputInterface $input): Configuration
    {
        $paths = PathResolver::resolve($input);
        $config = self::mergeInputRequirements($config, $input);
        $composer = self::getComposer($input, $paths[0]);
        /** @var string $tesPreset */
        $tesPreset = $config['preset'] ?? '';
        $preset = self::resolvePreset($tesPreset, $composer);
        $config['preset'] = $preset;
        $presetData = self::preparePreset($preset::get($composer), $config);
        $config = self::mergeConfig($presetData, $config);

        if ($composer->getName() === '') {
            $config = self::excludeGlobalInsights($config);
        }

        if (! isset($config['paths'])) {
            $config['paths'] = $paths;
        }

        $config['common_path'] = PathShortener::extractCommonPath((array) $config['paths']);

        return new Configuration($config);
    }

    public static function resolvePath(InputInterface $input): string
    {
        /** @var string|null $configPath */
        $configPath = $input->getOption('config-path');
        if ($configPath === null && file_exists(getcwd() . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME)) {
            $configPath = getcwd() . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;
        }

        return $configPath ?? '';
    }

    /**
     * Guesses the preset based in information from composer.
     */
    public static function guess(Composer $composer): string
    {
        $preset = self::guessPresetClass('', $composer);
        if ($preset !== '') {
            return $preset::getName();
        }

        return 'default';
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

    /**
     * Merge requirements config from console input.
     *
     * @param array<string, string|array> $config
     *
     * @return array<string, string|array>
     */
    private static function mergeInputRequirements(array $config, InputInterface $input): array
    {
        $requirements = Configuration::getAcceptedRequirements();
        foreach ($requirements as $requirement) {
            if ($input->hasParameterOption('--' . $requirement)) {
                $config['requirements'][$requirement] = $input->getOption($requirement);
            }
        }

        return $config;
    }

    private static function getComposer(InputInterface $input, string $path): Composer
    {
        /** @var string|null $composerPath */
        $composerPath = $input->hasOption('composer') ? $input->getOption('composer') : null;

        if ($composerPath === null) {
            $composerPath = rtrim($path, '/') . DIRECTORY_SEPARATOR . self::COMPOSER_FILENAME;
        }

        if (strpos($composerPath, self::COMPOSER_FILENAME) === false || ! file_exists($composerPath)) {
            return new Composer([]);
        }

        return Composer::fromPath($composerPath);
    }

    /**
     * @param array<string, string|array> $config
     *
     * @return array<string, string|array>
     */
    private static function excludeGlobalInsights(array $config): array
    {
        foreach (Kernel::getGlobalInsights() as $insight) {
            $config['remove'][] = $insight;
        }

        return $config;
    }

    /**
     * @param array<string, array|string|int> $preset
     * @param array<string, array|string> $config
     *
     * @return array<string, array|int|string>
     */
    private static function preparePreset(array $preset, array $config): array
    {
        $removedRulesByPreset = [];
        $addedRulesByConfig = [];

        if (isset($preset['remove']) && is_array($preset['remove']) && count($preset['remove']) > 0) {
            array_walk_recursive($preset['remove'], static function ($value) use (&$removedRulesByPreset): void {
                $removedRulesByPreset[] = $value;
            });
        }

        if (isset($config['add']) && is_array($config['add']) && count($config['add']) > 0) {
            array_walk_recursive($config['add'], static function ($value) use (&$addedRulesByConfig): void {
                $addedRulesByConfig[] = $value;
            });
        }

        $intersectRules = array_intersect($addedRulesByConfig, $removedRulesByPreset);

        // Config rules have more priority against preset rules, so we should override them.
        $preset['remove'] = array_diff($removedRulesByPreset, $intersectRules);

        return $preset;
    }

    private static function resolvePreset(string $testPreset, Composer $composer): string
    {
        if ($testPreset === '' || $testPreset === 'default') {
            return self::DEFAULT_PRESET;
        }

        $preset = self::guessPresetClass($testPreset, $composer);

        if (Configuration::isValidPreset($preset)) {
            return $preset;
        }

        throw new InvalidPresetException('A preset must implement the ' . Preset::class . ' interface');
    }

    private static function guessPresetClass(string $preset, Composer $composer): string
    {
        foreach (self::PRESETS as $presetClass) {
            if ($presetClass::shouldBeApplied($composer) || $preset === $presetClass::getName()) {
                return $presetClass;
            }
        }

        return $preset;
    }
}
