<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFound;
use SplFileInfo;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class InsightCollectionFactory
{
    private FilesRepository $filesRepository;

    private Analyser $analyser;

    private Configuration $config;

    /**
     * Creates a new instance of InsightCollection Factory.
     */
    public function __construct(FilesRepository $filesRepository, Analyser $analyser, Configuration $config)
    {
        $this->filesRepository = $filesRepository;
        $this->analyser = $analyser;
        $this->config = $config;
    }

    /**
     * @param array<string> $metrics
     */
    public function get(array $metrics, OutputInterface $consoleOutput): InsightCollection
    {
        $paths = $this->config->getPaths();
        $commonPath = $this->config->getCommonPath();

        try {
            $files = array_map(
                static fn (SplFileInfo $file) => $file->getRealPath(),
                $this->filesRepository->within($paths, $this->config->getExcludes())->getFiles()
            );
        } catch (InvalidArgumentException $exception) {
            throw new DirectoryNotFound($exception->getMessage(), 0, $exception);
        }

        $collector = $this->analyser->analyse($paths, $files, $commonPath);

        $insightsClasses = [];
        foreach ($metrics as $metricClass) {
            $insightsClasses = [...$insightsClasses, ...$this->getInsights($metricClass)];
        }

        $insightFactory = new InsightFactory($this->filesRepository, $insightsClasses, $this->config);
        $insightsForCollection = [];

        foreach ($metrics as $metricClass) {
            $insightsForCollection[$metricClass] = array_map(
                function (string $insightClass) use ($insightFactory, $collector, $consoleOutput) {
                    if (! array_key_exists(Insight::class, class_implements($insightClass))) {
                        return $insightFactory->makeFrom(
                            $insightClass,
                            $consoleOutput
                        );
                    }

                    return new $insightClass($collector, $this->config->getConfigForInsight($insightClass));
                },
                $this->getInsights($metricClass),
            );
        }

        return new InsightCollection($collector, $insightsForCollection);
    }

    /**
     * Returns the `Insights` from the given metric class.
     *
     * @return array<string>
     */
    private function getInsights(string $metricClass): array
    {
        /** @var HasInsights $metric */
        $metric = new $metricClass();

        $insights = array_key_exists(HasInsights::class, class_implements($metricClass))
            ? $metric->getInsights()
            : [];

        $toAdd = $this->config->getAddedInsightsByMetric($metricClass);
        $insights = [...$insights, ...$toAdd];

        // Remove insights based on config.
        return array_diff($insights, $this->config->getRemoves());
    }
}
