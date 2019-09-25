<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
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
     * Analyser constructor.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory $insightCollectionFactory
     */
    public function __construct(InsightCollectionFactory $insightCollectionFactory)
    {
        $this->insightCollectionFactory = $insightCollectionFactory;
    }

    /**
     * Analyse the given dirs.
     *
     * @param Formatter $formatter
     * @param array<string, array> $config
     * @param string $dir
     * @param OutputInterface $consoleOutput
     *
     * @return  \NunoMaduro\PhpInsights\Domain\Results
     */
    public function analyse(
        Formatter $formatter,
        array $config,
        string $dir,
        OutputInterface $consoleOutput
    ): Results {
        $metrics = MetricsFinder::find();

        $insightCollection = $this->insightCollectionFactory
            ->get($metrics, $config, $dir, $consoleOutput);

        $formatter->format($insightCollection, $dir, $metrics);

        return $insightCollection->results();
    }
}
