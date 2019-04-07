<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Metric;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class Feedback
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]
     */
    private $insightsPerMetric = [];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * Creates a new instance of the feedback.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     * @param  array  $insightsPerMetric
     */
    public function __construct(Collector $collector, array $insightsPerMetric)
    {
        $this->collector = $collector;
        $this->insightsPerMetric = $insightsPerMetric;
    }

    /**
     * @return \NunoMaduro\PhpInsights\Domain\Collector
     */
    public function getCollector(): Collector
    {
        return $this->collector;
    }

    /**
     * Gets all insights.
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]
     */
    public function all(): array
    {
        $all = [];

        foreach ($this->insightsPerMetric as $metricClass => $insights) {
            foreach ($insights as $insight) {
                $all[] = $insight;
            }
        }

        return $all;
    }

    /**
     * Gets all insights from given metric.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Metric  $metric
     *
     * @return \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]
     */
    public function allFrom(Metric $metric): array
    {
        return $this->insightsPerMetric[get_class($metric)] ?? [];
    }

    /**
     * Returns the quality of the code taking in consideration the current insights.
     *
     * @return float
     */
    public function quality(): float
    {
        $total = count($this->all());
        $issuesNotFound = 0;

        foreach ($this->all() as $insight) {
            if (! $insight->hasIssue()) {
                $issuesNotFound++;
            }
        }

        return (bool) $issuesNotFound ? (($issuesNotFound * 100.0) / $total) : 100.0;
    }
}
