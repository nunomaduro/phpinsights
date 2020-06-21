<?php

declare(strict_types=1);

namespace Tests;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Analyser as DomainAnalyser;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Fakes\FakeFileRepository;
use Tests\Fakes\FakeInput;

abstract class TestCase extends BaseTestCase
{
    private string $initialArgv;

    /**
     * Call protected/private method of a class.
     *
     * @param  class-string  $class Instantiated object that we will run method on
     * @param  string  $methodName Method name to call
     * @param  array<int, mixed>  $parameters Array of parameters to pass into method
     *
     * @return mixed Method result
     *
     * @throws ReflectionException
     */
    final public static function invokeStaticMethod(
        string $class,
        string $methodName,
        array $parameters
    ) {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $parameters);
    }

    /**
     * Runs the whole analyser on the specified files only.
     *
     * @param string $preset
     * @param array<string>  $filePaths
     * @param array<string> $paths
     *
     * @return InsightCollection
     */
    final public function runAnalyserOnPreset(
        string $preset,
        array $filePaths,
        array $paths = []
    ) : InsightCollection {
        return $this->runAnalyserOnConfig(
            ['preset' => $preset],
            $filePaths,
            $paths
        );
    }

    /**
     * Runs the whole analyser on the specified files only.
     *
     * @param array<string, mixed> $config
     * @param array<string> $filePaths
     * @param array<string> $paths
     *
     * @return InsightCollection
     */
    final public function runAnalyserOnConfig(
        array $config,
        array $filePaths,
        array $paths = []
    ) : InsightCollection {
        if ($paths === []) {
            $paths = [\dirname($filePaths[0])];
        }

        $config = ConfigResolver::resolve($config, FakeInput::paths($paths));

        $container = Container::make();
        \assert($container instanceof \League\Container\Container);

        $configurationDefinition = $container->extend(Configuration::class);
        $configurationDefinition->setConcrete($config);

        $analyser = new DomainAnalyser();

        $fileRepository = new FakeFileRepository($filePaths);

        $insightCollectionFactory = new InsightCollectionFactory(
            $fileRepository,
            $analyser,
            $config
        );

        return $insightCollectionFactory->get(
            MetricsFinder::find(),
            new NullOutput()
        );
    }

    /**
     * Prepares a supplied fixture for a supplied sniffer class with the
     * supplied properties.
     *
     * @param class-string                        $sniffClassName
     * @param string                              $fixtureFile
     * @param array<string, string|array<string>> $properties
     * @return LocalFile
     * @throws ReflectionException
     */
    final public static function prepareFixtureWithSniff(
        string $sniffClassName,
        string $fixtureFile,
        array $properties = []
    ): LocalFile {
        $sniffs = [self::getFilePathFromClass($sniffClassName)];

        $config = new Config();
        $config->standards = [];

        $ruleName = str_replace(
            'Sniff',
            '',
            class_basename($sniffClassName)
        );

        /** @var Ruleset $ruleset */
        $ruleset = (new ReflectionClass(Ruleset::class))
            ->newInstanceWithoutConstructor();
        $ruleset->ruleset = [
            "PhpInsights.Sniffs.{$ruleName}" => [
                'properties' => $properties,
            ],
        ];

        $ruleset->registerSniffs($sniffs, [], []);
        $ruleset->populateTokenListeners();

        return new LocalFile($fixtureFile, $ruleset, $config);
    }

    /**
     * @param class-string $className
     * @return string
     * @throws \ReflectionException
     */
    final public static function getFilePathFromClass(string $className) : string
    {
        $reflector = new ReflectionClass($className);

        $filename = $reflector->getFileName();

        return $filename === false ? '' : $filename;
    }


    protected function setUp(): void
    {
        // Replace temporarily current binary by phpinsights one, to execute subprocess
        $this->initialArgv = $_SERVER['argv'][0];
        $_SERVER['argv'][0] = getcwd() . '/bin/phpinsights';

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    protected function tearDown(): void
    {
        $_SERVER['argv'][0] = $this->initialArgv;
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

}
