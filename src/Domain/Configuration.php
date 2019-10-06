<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Application\Adapters\Drupal\Preset as DrupalPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Magento2\Preset as Magento2Preset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Yii\Preset as YiiPreset;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\Metric;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class Configuration
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
     * @var string
     */
    private $preset = 'default';

    /**
     * Directory to analyse.
     *
     * @var string
     */
    private $directory;

    /**
     * List of folder to exclude from analyse.
     *
     * @var array<string>
     */
    private $exclude;

    /**
     * List of insights added by metrics.
     *
     * @var array<string, array<string>>
     */
    private $add;

    /**
     * List of insights class to remove.
     *
     * @var array<string>
     */
    private $remove;

    /**
     * List of custom configuration by insight.
     *
     * @var array<string, array<string, string|int|array>>
     */
    private $config;

    /**
     * Configuration constructor.
     *
     * @param array<string, string|array> $config
     */
    public function __construct(array $config)
    {
        $this->resolveConfig($config);
    }

    /**
     * @return array<string, array<string>>
     */
    public function getAdd(): array
    {
        return $this->add;
    }

    /**
     * @param string $metric
     *
     * @return array<string>
     */
    public function getAddedInsightsByMetric(string $metric): array
    {
        return array_key_exists($metric, $this->add)
            ? $this->add[$metric]
            : [];
    }

    /**
     * @return array<string, array<string, string|int|array>>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param string $insight
     *
     * @return array<string, string|int|array>
     */
    public function getConfigForInsight(string $insight): array
    {
        return $this->config[$insight] ?? [];
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return array<string>
     */
    public function getExclude(): array
    {
        return $this->exclude;
    }

    /**
     * @return array<string>
     */
    public function getRemove(): array
    {
        return $this->remove;
    }

    /**
     * @return string
     */
    public function getPreset(): string
    {
        return $this->preset;
    }

    /**
     * @param array<string, string|array> $config
     */
    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'preset' => 'default',
            'directory' => (string) getcwd(),
            'exclude' => [],
            'add' => [],
            'remove' => [],
            'config' => [],
        ]);

        $resolver->setAllowedValues('preset', array_map(static function (string $presetClass) {
            return $presetClass::getName();
        }, self::$presets));
        $resolver->setAllowedValues('add', $this->validateAddedInsight());
        $resolver->setAllowedValues('config', $this->validateConfigInsights());
        $config = $resolver->resolve($config);

        $this->preset = $config['preset'];
        $this->directory = $config['directory'];
        $this->exclude = $config['exclude'];
        $this->add = $config['add'];
        $this->remove = $config['remove'];
        $this->config = $config['config'];
    }

    private function validateAddedInsight(): \Closure
    {
        return static function ($values) {
            foreach ($values as $metric => $insights) {
                if (! class_exists($metric) ||
                    ! in_array(Metric::class, class_implements($metric), true)
                ) {
                    throw new InvalidConfiguration(sprintf(
                        'Unable to use "%s" class as metric in section add.',
                        $metric
                    ));
                }
                if (! is_array($insights)) {
                    throw new InvalidConfiguration(sprintf(
                        'Added insights for metric "%s" should be an array.',
                        $metric
                    ));
                }

                foreach ($insights as $insight) {
                    if (! class_exists($insight)) {
                        throw new InvalidConfiguration(sprintf(
                            'Unable to add "%s" insight, class doesn\'t exists.',
                            $insight
                        ));
                    }
                }
            }
            return true;
        };
    }

    private function validateConfigInsights(): \Closure
    {
        return static function ($values) {
            foreach (array_keys($values) as $insight) {
                if (! class_exists((string) $insight)) {
                    throw new InvalidConfiguration(sprintf(
                        'Unable to config "%s" insight, class doesn\'t exists.',
                        $insight
                    ));
                }
            }
            return true;
        };
    }
}
