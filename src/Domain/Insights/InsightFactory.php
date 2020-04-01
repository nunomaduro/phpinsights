<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

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
 */
final class InsightFactory
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * @var array<string>
     */
    private $insightsClasses;

    /**
     * @var array<InsightContract>
     */
    private $insights = [];

    /**
     * @var array<InsightLoader>
     */
    private $insightLoaders;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Configuration
     */
    private $config;

    /** @var bool */
    private $ran = false;

    /**
     * Creates a new instance of Insight Factory.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
     * @param array<string> $insightsClasses
     * @param \NunoMaduro\PhpInsights\Domain\Configuration $config
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
     * @param string $errorClass
     * @param OutputInterface $consoleOutput
     *
     * @return InsightContract
     *
     * @throws \Exception
     */
    public function makeFrom(
        string $errorClass,
        OutputInterface $consoleOutput
    ): InsightContract {
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
        if ($this->ran === true) {
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
     * Return instancied insights.
     *
     * @param array<string> $insights
     *
     * @return array<InsightContract>
     */
    private function loadInsights(array $insights): array
    {
        $insightsAdded = [];

        foreach ($insights as $insight) {
            /** @var InsightLoader $loader */
            foreach ($this->insightLoaders as $loader) {
                if ($loader->support($insight)) {
                    $insightsAdded[] = $loader->load(
                        $insight,
                        (string) (getcwd() ?? $this->config->getCommonPath()),
                        $this->config->getConfigForInsight($insight)
                    );
                }
            }
        }

        return $insightsAdded;
    }
}
