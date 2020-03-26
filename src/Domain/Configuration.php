<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Closure;
use NunoMaduro\PhpInsights\Application\Adapters\Drupal\Preset as DrupalPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Magento2\Preset as Magento2Preset;
use NunoMaduro\PhpInsights\Application\Adapters\Symfony\Preset as SymfonyPreset;
use NunoMaduro\PhpInsights\Application\Adapters\Yii\Preset as YiiPreset;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter as FileLinkFormatterContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Metric;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
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

    /** @var array<string> */
    private static $acceptedRequirements = [
        'min-quality',
        'min-complexity',
        'min-architecture',
        'min-style',
        'disable-security-check',
    ];

    /**
     * @var string
     */
    private $preset = 'default';

    /**
     * List of directories to analyse.
     *
     * @var array<string>
     */
    private $directories;

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
     * List of requirements.
     *
     * @var array<string>
     */
    private $requirements;

    /**
     * List of custom configuration by insight.
     *
     * @var array<string, array<string, string|int|array>>
     */
    private $config;

    /**
     * @var FileLinkFormatterContract
     */
    private $fileLinkFormatter;

    /**
     * Configuration constructor.
     *
     * @param array<string, string|array|null> $config
     */
    public function __construct(array $config)
    {
        $this->fileLinkFormatter = new NullFileLinkFormatter();
        $this->resolveConfig($config);
    }

    /**
     * @return array<string>
     */
    public static function getAcceptedRequirements(): array
    {
        return self::$acceptedRequirements;
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
     * @return array<string>
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return array<string>
     */
    public function getExcludes(): array
    {
        return $this->exclude;
    }

    /**
     * @return array<string>
     */
    public function getRemoves(): array
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
     * @return float
     */
    public function getMinQuality(): float
    {
        return (float) ($this->requirements['min-quality'] ?? 0);
    }

    /**
     * @return float
     */
    public function getMinComplexity(): float
    {
        return (float) ($this->requirements['min-complexity'] ?? 0);
    }

    /**
     * @return float
     */
    public function getMinArchitecture(): float
    {
        return (float) ($this->requirements['min-architecture'] ?? 0);
    }

    /**
     * @return float
     */
    public function getMinStyle(): float
    {
        return (float) ($this->requirements['min-style'] ?? 0);
    }

    /**
     * @return bool
     */
    public function isSecurityCheckDisabled(): bool
    {
        return (bool) ($this->requirements['disable-security-check'] ?? false);
    }

    /**
     * @return FileLinkFormatterContract
     */
    public function getFileLinkFormatter(): FileLinkFormatterContract
    {
        return $this->fileLinkFormatter;
    }

    /**
     * @param array<string, string|array|null> $config
     */
    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'preset' => 'default',
            'directory' => (string) getcwd(),
            'exclude' => [],
            'add' => [],
            'requirements' => [],
            'remove' => [],
            'config' => [],
        ]);

        $resolver->setDefined('ide');
        $resolver->setAllowedValues('preset', array_map(static function (string $presetClass) {
            return $presetClass::getName();
        }, self::$presets));
        $resolver->setAllowedValues('add', $this->validateAddedInsight());
        $resolver->setAllowedValues('config', $this->validateConfigInsights());
        $resolver->setAllowedValues('requirements', $this->validateRequirements());
        $config = $resolver->resolve($config);

        $this->preset = $config['preset'];

        foreach ((array) $config['directory'] as $directory) {
            // resolve symbolic link, /./, /../
            $this->directories[] = realpath($directory) !== false
                ? realpath($directory)
                : $directory;
        }

        $this->exclude = $config['exclude'];
        $this->add = $config['add'];
        $this->remove = $config['remove'];
        $this->config = $config['config'];
        $this->requirements = $config['requirements'];

        if (
            array_key_exists('ide', $config)
            && is_string($config['ide'])
            && $config['ide'] !== ''
        ) {
            $this->fileLinkFormatter = $this->resolveIde((string) $config['ide']);
        }
    }

    private function validateAddedInsight(): \Closure
    {
        return static function ($values): bool {
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
                        'Added insights for metric "%s" should be in an array.',
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
        return static function ($values): bool {
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

    private function resolveIde(string $ide): FileLinkFormatterContract
    {
        $links = [
            'textmate' => 'txmt://open?url=file://%f&line=%l',
            'macvim' => 'mvim://open?url=file://%f&line=%l',
            'emacs' => 'emacs://open?url=file://%f&line=%l',
            'sublime' => 'subl://open?url=file://%f&line=%l',
            'phpstorm' => 'phpstorm://open?file=%f&line=%l',
            'atom' => 'atom://core/open/file?filename=%f&line=%l',
            'vscode' => 'vscode://file/%f:%l',
        ];

        if (isset($links[$ide]) === false &&
            mb_strpos((string) $ide, '://') === false) {
            throw new InvalidConfiguration(sprintf(
                'Unknow IDE "%s". Try one in this list [%s] or provide pattern link handler',
                $ide,
                implode(', ', array_keys($links))
            ));
        }

        $fileFormatterPattern = $links[$ide] ?? $ide;

        return new FileLinkFormatter($fileFormatterPattern);
    }

    private function validateRequirements(): Closure
    {
        return static function ($values): bool {
            $invalidValues = array_diff(
                array_keys($values),
                self::getAcceptedRequirements()
            );
            if ($invalidValues !== []) {
                throw new InvalidConfiguration(sprintf(
                    'Unknown requirements [%s], valid values are [%s].',
                    implode(', ', $invalidValues),
                    implode(', ', self::getAcceptedRequirements())
                ));
            }
            return true;
        };
    }
}
