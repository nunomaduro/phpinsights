<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\MetricsFinder;

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
     * @param  array<string, array>  $config
     * @param  string  $dir
     *
     * @return float
     */
    public function analyse(Style $style, array $config, string $dir): float
    {
        $metrics = MetricsFinder::find();

        $insightCollection = $this->insightCollectionFactory->get($metrics, $config, $dir);

        $results = $insightCollection->results();

        $style->header($results, $dir)
            ->code($insightCollection, $results)
            ->complexity($insightCollection, $results)
            ->architecture($insightCollection, $results)
            ->style($results);

        $stdin = fopen('php://stdin', 'r');
        if ($stdin !== false) {
            $style->newLine(1);
            $style->write('<title>Press any key to continue...</title>');
            fgetc($stdin);
        }


        $style->issues($insightCollection, $metrics, $dir);

        return $results->getCodeQuality();
    }
}
