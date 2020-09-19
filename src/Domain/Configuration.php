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
 *
 * @see \Tests\Domain\ConfigurationTest
 */
final class Configuration
{
    private const PRESETS = [
        DrupalPreset::class,
        LaravelPreset::class,
        SymfonyPreset::class,
        YiiPreset::class,
        Magento2Preset::class,
        DefaultPreset::class,
    ];

    private const ACCEPTED_REQUIREMENTS = [
        'min-quality',
        'min-complexity',
        'min-architecture',
        'min-style',
        'disable-security-check',
    ];

    private const LINKS = [
        'textmate' => 'txmt://open?url=file://%f&line=%l',
        'macvim' => 'mvim://open?url=file://%f&line=%l',
        'emacs' => 'emacs://open?url=file://%f&line=%l',
        'sublime' => 'subl://open?url=file://%f&line=%l',
        'phpstorm' => 'phpstorm://open?file=%f&line=%l',
        'atom' => 'atom://core/open/file?filename=%f&line=%l',
        'vscode' => 'vscode://file/%f:%l',
    ];

    private string $preset = 'default';

    /**
     * List of paths to analyse.
     *
     * @var array<string>
     */
    private array $paths;

    private string $commonPath;

    /**
     * List of folder to exclude from analyse.
     *
     * @var array<string>
     */
    private array $exclude;

    /**
     * List of insights added by metrics.
     *
     * @var array<string, array<string>>
     */
    private array $add;

    /**
     * List of insights class to remove.
     *
     * @var array<string>
     */
    private array $remove;

    /**
     * List of requirements.
     *
     * @var array<string>
     */
    private array $requirements;

    /**
     * List of custom configuration by insight.
     *
     * @var array<string, array<string, string|int|array>>
     */
    private array $config;

    private FileLinkFormatterContract $fileLinkFormatter;

    private bool $fix;

    private string $cacheKey;

    private int $threads;

    /**
     * Configuration constructor.
     *
     * @param array<string, string|int|array|null> $config
     */
    public function __construct(array $config)
    {
        $this->fileLinkFormatter = new NullFileLinkFormatter();
        $this->resolveConfig($config);
        $this->cacheKey = md5(\json_encode($config, JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<string>
     */
    public static function getAcceptedRequirements(): array
    {
        return self::ACCEPTED_REQUIREMENTS;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getAdd(): array
    {
        return $this->add;
    }

    /**
     * @return array<string>
     */
    public function getAddedInsightsByMetric(string $metric): array
    {
        return $this->add[$metric] ?? [];
    }

    /**
     * @return array<string, array<string, string|int|array>>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return array<string, string|int|array>
     */
    public function getConfigForInsight(string $insight): array
    {
        return $this->config[$insight] ?? [];
    }

    /**
     * @return array<string>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function getCommonPath(): string
    {
        return $this->commonPath;
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

    public function getPreset(): string
    {
        return $this->preset;
    }

    public function getMinQuality(): float
    {
        return (float) ($this->requirements['min-quality'] ?? 0);
    }

    public function getMinComplexity(): float
    {
        return (float) ($this->requirements['min-complexity'] ?? 0);
    }

    public function getMinArchitecture(): float
    {
        return (float) ($this->requirements['min-architecture'] ?? 0);
    }

    public function getMinStyle(): float
    {
        return (float) ($this->requirements['min-style'] ?? 0);
    }

    public function isSecurityCheckDisabled(): bool
    {
        return (bool) ($this->requirements['disable-security-check'] ?? false);
    }

    public function getFileLinkFormatter(): FileLinkFormatterContract
    {
        return $this->fileLinkFormatter;
    }

    public function hasFixEnabled(): bool
    {
        return $this->fix;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function getNumberOfThreads(): int
    {
        return $this->threads;
    }

    /**
     * @param array<string, string|int|array|null> $config
     */
    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'preset' => 'default',
            'paths' => [(string) getcwd()],
            'common_path' => '',
            'exclude' => [],
            'add' => [],
            'requirements' => [],
            'remove' => [],
            'config' => [],
            'fix' => false,
        ]);

        $resolver->setDefined('ide');
        $resolver->setDefined('threads');
        $resolver->setAllowedValues(
            'preset',
            array_map(static fn (string $presetClass) => $presetClass::getName(), self::PRESETS)
        );

        $resolver->setAllowedValues('add', $this->validateAddedInsight());
        $resolver->setAllowedValues('config', $this->validateConfigInsights());
        $resolver->setAllowedValues('requirements', $this->validateRequirements());
        $resolver->setAllowedTypes('threads', ['null', 'int']);
        $resolver->setAllowedValues('threads', static fn ($value) => $value === null || $value >= 1);

        $config = $resolver->resolve($config);

        $this->preset = $config['preset'];

        foreach ((array) $config['paths'] as $path) {
            // resolve symbolic link, /./, /../
            $this->paths[] = realpath($path) !== false
                ? realpath($path)
                : $path;
        }

        $this->commonPath = $config['common_path'];
        $this->exclude = $config['exclude'];
        $this->add = $config['add'];
        $this->remove = $config['remove'];
        $this->config = $config['config'];
        $this->requirements = $config['requirements'];
        $this->fix = $config['fix'];

        if (array_key_exists('ide', $config)
            && is_string($config['ide'])
            && $config['ide'] !== ''
        ) {
            $this->fileLinkFormatter = $this->resolveIde($config['ide']);
        }
        $this->threads = $config['threads'] ?? $this->getNumberOfCore();
    }

    private function validateAddedInsight(): Closure
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

                if (! \is_array($insights)) {
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

    private function validateConfigInsights(): Closure
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
        if (! isset(self::LINKS[$ide]) &&
            mb_strpos($ide, '://') === false) {
            throw new InvalidConfiguration(sprintf(
                'Unknown IDE "%s". Try one in this list [%s] or provide pattern link handler',
                $ide,
                implode(', ', array_keys(self::LINKS))
            ));
        }

        $fileFormatterPattern = self::LINKS[$ide] ?? $ide;

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

    /**
     * @see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
     */
    private function getNumberOfCore(): int
    {
        $cores = 2;
        if (is_file('/proc/cpuinfo')) {
            // Linux (and potentially Windows with linux sub systems)
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== false) {
                preg_match_all('/^processor/m', $cpuinfo, $matches);
                return \count($matches[0]);
            }
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            // Windows
            $process = @popen('wmic cpu get NumberOfLogicalProcessors', 'rb');
            if ($process !== false) {
                fgets($process);
                $cores = (int) fgets($process);
                pclose($process);
            }

            return $cores;
        }

        $process = @\popen('sysctl -n hw.ncpu', 'rb');
        if ($process !== false) {
            // *nix (Linux, BSD and Mac)
            $cores = (int) fgets($process);
            pclose($process);
        }

        return $cores;
    }
}
