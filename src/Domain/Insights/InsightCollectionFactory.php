<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Contracts\{HasInsights, Insight};
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFoundException;
use Symfony\Component\DependencyInjection\Container;
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
     * @param  string[]  $metrics
     * @param  array<string, array<string, string>>  $config
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection
     */
    public function get(array $metrics, array $config, string $dir): InsightCollection
    {
        $metrics = array_filter($metrics, function ($metricClass) {
            return class_exists($metricClass);
        });

        try {
            $files = array_map(function (SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->in($dir, $config['exclude'] ?? [])->getFiles()));
        } catch (InvalidArgumentException $e) {
            throw new DirectoryNotFoundException($e->getMessage());
        }

        $collector = $this->analyser->analyse($files);
        /** @var Container $container */

        $insightsClasses = [];
        foreach ($metrics as $metricClass) {
            $insightsClasses = array_merge($insightsClasses, $this->getInsights($metricClass, $config));
        }

        $insightFactory = new InsightFactory($this->filesRepository, $dir, $insightsClasses);
        $insightsForCollection = [];
        foreach ($metrics as $metricClass) {
            $insightsForCollection[$metricClass] = array_map(function (string $insightClass) use ($insightFactory, $collector, $config) {
                if (! array_key_exists(Insight::class, class_implements($insightClass))) {
                    return $insightFactory->makeFrom($insightClass);
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
     * @param  array<string, array<string, string>>  $config
     *
     * @return string[]
     */
    private function getInsights(string $metricClass, array $config): array
    {
        $metric = new $metricClass;

        $insights = array_key_exists(HasInsights::class, class_implements($metricClass)) ? $metric->getInsights() : [];

        $insights = array_merge($insights, $config['add'][$metricClass] ?? []);

        return array_diff($insights, $config['remove'] ?? []);
    }
}
