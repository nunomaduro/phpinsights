<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFound;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class InsightCollectionFactory
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Analyser
     */
    private $analyser;

    /**
     * Creates a new instance of InsightCollection Factory.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     * @param  \NunoMaduro\PhpInsights\Domain\Analyser  $analyser
     */
    public function __construct(FilesRepository $filesRepository, Analyser $analyser)
    {
        $this->filesRepository = $filesRepository;
        $this->analyser = $analyser;
    }

    /**
     * @param array<string> $metrics
     * @param  array<string, array<string, string>>  $config
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection
     */
    public function get(array $metrics, array $config, string $dir): InsightCollection
    {
        try {
            $files = array_map(static function (SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->within($dir, $config['exclude'] ?? [])->getFiles()));
        } catch (\InvalidArgumentException $exception) {
            throw new DirectoryNotFound($exception->getMessage(), 0, $exception);
        }

        $collector = $this->analyser->analyse($dir, $files);
        /** @var \Symfony\Component\DependencyInjection\Container $container */

        $insightsClasses = [];
        foreach ($metrics as $metricClass) {
            $insightsClasses = array_merge($insightsClasses, $this->getInsights($metricClass, $config));
        }

        $insightFactory = new InsightFactory($this->filesRepository, $dir, $insightsClasses);
        $insightsForCollection = [];
        foreach ($metrics as $metricClass) {
            $insightsForCollection[$metricClass] = array_map(static function (string $insightClass) use ($insightFactory, $collector, $config) {
                if (! array_key_exists(Insight::class, class_implements($insightClass))) {

                    return $insightFactory->makeFrom(
                        $insightClass,
                        $config
                    );
                }

                return new $insightClass($collector, $config['config'][$insightClass] ?? []);
            }, $this->getInsights($metricClass, $config));
        }

        return new InsightCollection($collector, $insightsForCollection);
    }

    /**
     * Returns the `Insights` from the given metric class.
     *
     * @param  string  $metricClass
     * @param  array<string, array<string, string|array>>  $config
     *
     * @return array<string>
     */
    private function getInsights(string $metricClass, array $config): array
    {
        /** @var HasInsights $metric */
        $metric = new $metricClass();

        $insights = array_key_exists(
            HasInsights::class,
            class_implements($metricClass)
        ) ? $metric->getInsights() : [];

        $toAdd = array_key_exists('add', $config) &&
        array_key_exists($metricClass, $config['add']) &&
        is_array($config['add'][$metricClass])
            ? $config['add'][$metricClass]
            : [];

        $insights = array_merge($insights, $toAdd);

        // Remove insights based on config.
        $insights = array_diff($insights, $config['remove'] ?? []);

        return $insights;
    }
}
