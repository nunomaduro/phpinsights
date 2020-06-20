<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\DetailsCarrier;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor as FileProcessorContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader as InsightLoaderContract;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\InsightLoader\InsightLoader;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class InternalProcessorCommand
{
    public const NAME = 'internal:processors';

    private CacheInterface $cache;

    private Configuration $configuration;

    private array $insightsLoaders;

    private array $allInsights = [];

    private $filesProcessors;

    public function __construct(CacheInterface $cache, Configuration $configuration)
    {
        $this->cache = $cache;
        $this->configuration = $configuration;
        $container = Container::make();

        $this->filesProcessors = $container->get(FileProcessorContract::FILE_PROCESSOR_TAG);
        $loaders = $container->get(InsightLoaderContract::INSIGHT_LOADER_TAG);

        // exclude InsightLoader, not used here
        $this->insightsLoaders = array_filter($loaders, static function (InsightLoaderContract $loader) {
            return ! $loader instanceof InsightLoader;
        });
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $files = $input->getArgument('files');
        if (! \is_array($files) || \count($files) === 0) {
            return 0;
        }

        $metrics = MetricsFinder::find();
        $insightsClasses = [];
        foreach ($metrics as $metricClass) {
            $insightsClasses = [...$insightsClasses, ...$this->loadInsights($metricClass)];
        }
        $this->allInsights = $insightsClasses;

        set_error_handler(static function (int $errno, string $errstr): bool {
            throw new \RuntimeException($errstr, $errno);
        }, E_NOTICE);

        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            $this->processFile(new SplFileInfo($file, $fileInfo['dirname'], $file));
        }

        return 0;
    }

    /**
     * Returns the `Insights` from the given metric class.
     *
     * @return array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>
     */
    private function loadInsights(string $metricClass): array
    {
        /** @var HasInsights $metric */
        $metric = new $metricClass();

        $insights = \array_key_exists(HasInsights::class, class_implements($metricClass))
            ? $metric->getInsights()
            : [];

        $toAdd = $this->configuration->getAddedInsightsByMetric($metricClass);
        $insights = [...$insights, ...$toAdd];

        // Remove insights based on config.
        $insights = array_diff($insights, $this->configuration->getRemoves());

        $insightsAdded = [];
        $path = (string) (getcwd() ?? $this->configuration->getCommonPath());
        $collector = new Collector([], $path);
        foreach ($insights as $insight) {
            /** @var InsightLoader $loader */
            foreach ($this->insightsLoaders as $loader) {
                if ($loader->support($insight)) {
                    $insightsAdded[] = $loader->load(
                        $insight,
                        $path,
                        $this->configuration->getConfigForInsight($insight),
                        $collector
                    );
                }
            }
        }

        foreach ($insightsAdded as $insight) {
            /** @var FileProcessorContract $processor */
            foreach ($this->filesProcessors as $processor) {
                if ($processor->support($insight)) {
                    $processor->addChecker($insight);
                }
            }
        }

        return $insightsAdded;
    }

    private function processFile(SplFileInfo $file): void
    {
        $cacheKey = 'insights.' . $this->configuration->getCacheKey() . '.' . md5($file->getContents());
        // Do not use cache if fix is enabled to force processors to handle it
        if ($this->configuration->hasFixEnabled() === false && $this->cache->has($cacheKey)) {
            return;
        }
        /** @var FileProcessorContract $fileProcessor */
        foreach ($this->filesProcessors as $fileProcessor) {
            $fileProcessor->processFile($file);
        }

        if ($this->configuration->hasFixEnabled() === true) {
            // regenerate cache key in case fixer change contents
            $cacheKey = 'insights.' . $this->configuration->getCacheKey() . '.' . md5($file->getContents());
        }

        $this->cacheDetailsForFile($cacheKey, $file);

        if ($this->configuration->hasFixEnabled()) {
            $cacheKey = str_replace('insights.', 'fix.', $cacheKey);
            $this->cacheFixForFile($cacheKey, $file);
        }
    }

    private function cacheDetailsForFile(string $cacheKey, SplFileInfo $file): void
    {
        $detailsByInsights = [];
        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($this->allInsights as $insight) {
            if (! $insight instanceof DetailsCarrier || ! $insight->hasIssue()) {
                continue;
            }
            $details = array_filter(
                $insight->getDetails(),
                static fn (Details $detail): bool => $detail->getFile() === $file->getRealPath()
            );
            $detailsByInsights[$insight->getInsightClass()] = $details;
        }

        $this->cache->set($cacheKey, $detailsByInsights);
    }

    private function cacheFixForFile(string $cacheKey, SplFileInfo $file): void
    {
        $fixByInsights = [];
        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($this->allInsights as $insight) {
            if (! $insight instanceof Fixable || ! $insight->getTotalFix() === 0) {
                continue;
            }
            $details = array_filter(
                $insight->getFixPerFile(),
                static fn (Details $detail): bool => $detail->getFile() === $file->getRealPath()
            );
            $fixByInsights[$insight->getInsightClass()] = $details;
        }

        $this->cache->set($cacheKey, $fixByInsights);
    }
}
