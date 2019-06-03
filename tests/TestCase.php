<?php

declare(strict_types=1);

namespace Tests;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Analyser as DomainAnalyser;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
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
    public function invokeStaticMethod(string $class, string $methodName, array $parameters)
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $parameters);
    }

    /**
     * Runs the whole analyser on the specified files only.
     *
     * @param string $preset
     * @param array  $filePaths
     * @param string $dir
     * @return InsightCollection
     */
    public function runAnalyserOnPreset(string $preset, array $filePaths, string $dir = "") : InsightCollection
    {
        $config = ConfigResolver::resolve(['preset' => $preset], $dir);

        $analyser = new DomainAnalyser();

        $fileRepository = new FakeFileRepository($filePaths);

        $insightCollectionFactory = new InsightCollectionFactory($fileRepository, $analyser);

        return $insightCollectionFactory->get(
            MetricsFinder::find(),
            $config,
            $dir
        );
    }
}
