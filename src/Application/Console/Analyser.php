<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use NunoMaduro\PhpInsights\Domain\Results;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Analyser
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory
     */
    private $insightCollectionFactory;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Configuration
     */
    private $config;

    /**
     * Analyser constructor.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory $insightCollectionFactory
     * @param \NunoMaduro\PhpInsights\Domain\Configuration $config
     */
    public function __construct(
        InsightCollectionFactory $insightCollectionFactory,
        Configuration $config
    ) {
        $this->insightCollectionFactory = $insightCollectionFactory;
        $this->config = $config;
    }

    /**
     * Analyse the given dirs.
     *
     * @param Formatter $formatter
     * @param OutputInterface $consoleOutput
     *
     * @return  \NunoMaduro\PhpInsights\Domain\Results
     */
    public function analyse(
        Formatter $formatter,
        OutputInterface $consoleOutput
    ): Results {
        $metrics = MetricsFinder::find();

        $insightCollection = $this->insightCollectionFactory
            ->get($metrics, $consoleOutput);

        $formatter->format($insightCollection, $this->config->getDirectories(), $metrics);

        return $insightCollection->results();
    }
}
