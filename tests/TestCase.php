<?php

declare(strict_types=1);

namespace Tests;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Analyser as DomainAnalyser;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;
use Tests\Fakes\FakeFileRepository;

abstract class TestCase extends BaseTestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param  string  $class Instantiated object that we will run method on
     * @param  string  $methodName Method name to call
     * @param  array<int, mixed>  $parameters Array of parameters to pass into method
     *
     * @return mixed Method result
     *
     * @throws ReflectionException
     */
    public function invokeStaticMethod(
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
     * @param string $dir
     * @return InsightCollection
     */
    public function runAnalyserOnPreset(
        string $preset,
        array $filePaths,
        string $dir = ""
    ) : InsightCollection {
        return $this->runAnalyserOnConfig(
            ['preset' => $preset],
            $filePaths,
            $dir
        );
    }

    /**
     * Runs the whole analyser on the specified files only.
     *
     * @param array<string, mixed> $config
     * @param array<string> $filePaths
     * @param string        $dir
     * @return InsightCollection
     */
    public function runAnalyserOnConfig(
        array $config,
        array $filePaths,
        string $dir = ""
    ) : InsightCollection {
        $config = ConfigResolver::resolve($config, $dir);

        $analyser = new DomainAnalyser();

        $fileRepository = new FakeFileRepository($filePaths);

        $insightCollectionFactory = new InsightCollectionFactory(
            $fileRepository,
            $analyser
        );


        return $insightCollectionFactory->get(
            MetricsFinder::find(),
            $config,
            $dir
        );
    }

    /**
     * Prepares a supplied fixture for a supplied sniffer class with the
     * supplied properties.
     *
     * @param string                              $sniffClassName
     * @param string                              $fixtureFile
     * @param array<string, string|array<string>> $properties
     * @return LocalFile
     * @throws ReflectionException
     */
    public static function prepareFixtureWithSniff(
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
            ]
        ];

        $ruleset->registerSniffs($sniffs, [], []);
        $ruleset->populateTokenListeners();
        return new LocalFile($fixtureFile, $ruleset, $config);
    }

    public static function getFilePathFromClass(string $className) : string
    {
        $reflector = new ReflectionClass($className);

        $filename = $reflector->getFileName();

        return $filename === false ? "" : $filename;
    }
}
