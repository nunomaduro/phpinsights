<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Metric;
use NunoMaduro\PhpInsights\Domain\Results;

/**
 * @internal
 */
final class InsightCollection
{
    /**
     * @var array<string, \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]>
     */
    private $insightsPerMetric = [];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * Creates a new instance of the Insight Collection.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     * @param  array<string, \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]>  $insightsPerMetric
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

        foreach ($this->insightsPerMetric as $insights) {
            foreach ($insights as $insight) {
                $all[] = $insight;
            }
        }

        return $all;
    }

    /**
     * Gets all insights.
     *
     * @return int
     */
    public function issuesCount(): int
    {
        $count = 0;

        foreach ($this->insightsPerMetric as $insights) {
            foreach ($insights as $insight) {
                if ($insight->hasIssue()) {
                    $count += 1;
                }
            }
        }

        return $count;
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
     * Returns the results of the code taking in consideration the current insights.
     *
     * @return \NunoMaduro\PhpInsights\Domain\Results
     */
    public function results(): Results
    {
        $perCategory = [];

        foreach ($this->insightsPerMetric as $metric => $insights) {
            $category = explode('\\', $metric);
            $category = $category[count($category) - 2];

            if (! array_key_exists($category, $perCategory)) {
                $perCategory[$category] = [];
            }

            $perCategory[$category] = array_merge(
                $perCategory[$category], $insights
            );
        }

        return new Results($perCategory);
    }
}
