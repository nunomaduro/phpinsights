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
    private InsightCollectionFactory $insightCollectionFactory;

    /**
     * Analyser constructor.
     */
    public function __construct(InsightCollectionFactory $insightCollectionFactory)
    {
        $this->insightCollectionFactory = $insightCollectionFactory;
    }

    /**
     * Analyse the given dirs.
     */
    public function analyse(
        Formatter $formatter,
        OutputInterface $consoleOutput
    ): Results {
        $metrics = MetricsFinder::find();

        $insightCollection = $this->insightCollectionFactory
            ->get($metrics, $consoleOutput);

        $formatter->format($insightCollection, $metrics);

        return $insightCollection->results();
    }
}
