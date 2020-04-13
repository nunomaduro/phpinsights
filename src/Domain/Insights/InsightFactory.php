<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use Exception;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Runner;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @see \Tests\Domain\Insights\InsightFactoryTest
 */
final class InsightFactory
{
    private FilesRepository $filesRepository;

    /**
     * @var array<string>
     */
    private array $insightsClasses;

    /**
     * @var array<InsightContract>
     */
    private array $insights = [];

    /**
     * @var array<InsightLoader>
     */
    private array $insightLoaders;

    private Configuration $config;

    private bool $ran = false;

    /**
     * Creates a new instance of Insight Factory.
     *
     * @param array<string> $insightsClasses
     */
    public function __construct(FilesRepository $filesRepository, array $insightsClasses, Configuration $config)
    {
        $this->filesRepository = $filesRepository;
        $this->insightsClasses = $insightsClasses;
        $this->insightLoaders = Container::make()->get(InsightLoader::INSIGHT_LOADER_TAG);
        $this->config = $config;
    }

    /**
     * Creates a Insight from the given error class.
     *
     * @throws Exception
     */
    public function makeFrom(string $errorClass, OutputInterface $consoleOutput): InsightContract
    {
        $this->runInsightCollector($consoleOutput);

        /** @var InsightContract $insight */
        foreach ($this->insights as $insight) {
            if ($insight->getInsightClass() === $errorClass) {
                return $insight;
            }
        }

        throw new RuntimeException(sprintf('Insight `%s` is not instantiable.', $errorClass));
    }

    private function runInsightCollector(OutputInterface $consoleOutput): void
    {
        if ($this->ran) {
            return;
        }

        $runner = new Runner(
            $consoleOutput,
            $this->filesRepository
        );

        // Add insights
        $insights = $this->loadInsights($this->insightsClasses);
        $this->insights = $insights;
        $runner->addInsights($insights);

        // Run it.
        $runner->run();
        $this->ran = true;
    }

    /**
     * Return instantiated insights.
     *
     * @param array<string> $insights
     *
     * @return array<InsightContract>
     */
    private function loadInsights(array $insights): array
    {
        $insightsAdded = [];
        $path = (string) (getcwd() ?? $this->config->getCommonPath());

        foreach ($insights as $insight) {
            /** @var InsightLoader $loader */
            foreach ($this->insightLoaders as $loader) {
                if ($loader->support($insight)) {
                    $insightsAdded[] = $loader->load(
                        $insight,
                        $path,
                        $this->config->getConfigForInsight($insight)
                    );
                }
            }
        }

        return $insightsAdded;
    }
}
