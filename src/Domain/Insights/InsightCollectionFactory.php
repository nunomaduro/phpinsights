<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFound;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @var \NunoMaduro\PhpInsights\Domain\Configuration
     */
    private $config;

    /**
     * Creates a new instance of InsightCollection Factory.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
     * @param \NunoMaduro\PhpInsights\Domain\Analyser $analyser
     * @param \NunoMaduro\PhpInsights\Domain\Configuration $config
     */
    public function __construct(
        FilesRepository $filesRepository,
        Analyser $analyser,
        Configuration $config
    ) {
        $this->filesRepository = $filesRepository;
        $this->analyser = $analyser;
        $this->config = $config;
    }

    /**
     * @param array<string> $metrics
     * @param OutputInterface $consoleOutput
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection
     */
    public function get(
        array $metrics,
        OutputInterface $consoleOutput
    ): InsightCollection {
        $dir = $this->config->getDirectory();

        try {
            $files = array_map(static function (\SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->within($dir, $this->config->getExclude())->getFiles()));
        } catch (\InvalidArgumentException $exception) {
            throw new DirectoryNotFound($exception->getMessage(), 0, $exception);
        }

        $collector = $this->analyser->analyse($dir, $files);

        $insightsClasses = [];
        foreach ($metrics as $metricClass) {
            $insightsClasses = array_merge($insightsClasses, $this->getInsights($metricClass));
        }

        $insightFactory = new InsightFactory($this->filesRepository, $dir, $insightsClasses, $this->config);
        $insightsForCollection = [];
        foreach ($metrics as $metricClass) {
            $insightsForCollection[$metricClass] = array_map(function (string $insightClass) use ($insightFactory, $collector, $consoleOutput) {
                if (! array_key_exists(Insight::class, class_implements($insightClass))) {
                    return $insightFactory->makeFrom(
                        $insightClass,
                        $consoleOutput
                    );
                }

                return new $insightClass($collector, $this->config->getConfigForInsight($insightClass));
            }, $this->getInsights($metricClass));
        }

        return new InsightCollection($collector, $insightsForCollection);
    }

    /**
     * Returns the `Insights` from the given metric class.
     *
     * @param string $metricClass
     *
     * @return array<string>
     */
    private function getInsights(string $metricClass): array
    {
        /** @var HasInsights $metric */
        $metric = new $metricClass();

        $insights = array_key_exists(
            HasInsights::class,
            class_implements($metricClass)
        ) ? $metric->getInsights() : [];

        $toAdd = $this->config->getAddedInsightsByMetric($metricClass);
        $insights = array_merge($insights, $toAdd);

        // Remove insights based on config.
        return array_diff($insights, $this->config->getRemove());
    }
}
