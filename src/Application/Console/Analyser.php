<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Helpers\Row;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;
use NunoMaduro\PhpInsights\Domain\Results;

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
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory  $insightCollectionFactory
     */
    public function __construct(InsightCollectionFactory $insightCollectionFactory)
    {
        $this->insightCollectionFactory = $insightCollectionFactory;
    }

    /**
     * Analyse the given dirs.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Style  $style
     * @param  array  $config
     * @param  string  $dir
     *
     * @return float
     */
    public function analyse(Style $style, array $config, string $dir): float
    {
        $metrics = MetricsFinder::find();

        $insightCollection = $this->insightCollectionFactory->get($metrics, $config, $dir);

        $style->newLine(2);

        $style->writeln(sprintf('<fg=yellow>[%s]</> `%s`', date('Y-m-d H:i:s'), $dir));

        $style->header($results = $insightCollection->results());

        $style->code($insightCollection, $results);

        $style->complexity($insightCollection, $results);

        $style->structure($insightCollection, $results);

        $style->dependencies($insightCollection, $results, $dir);

        foreach ($metrics as $metricClass) {
            (new Row($insightCollection, $metricClass))->writeIssues($style, $dir);
        }

        return $results->getCodeQuality();
    }
}
