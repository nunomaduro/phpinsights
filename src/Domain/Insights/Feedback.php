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
    private $insights = [];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * Creates a new instance of the feedback.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]  $insights
     */
    public function __construct(Collector $collector, array $insights)
    {
        $this->collector = $collector;

        foreach ($insights as $insight) {
            $this->insights[get_class($insight)] = $insight;
        }
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
        return $this->insights;
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
        if (! $metric instanceof HasInsights) {
            return [];
        }

        $insightsClasses = array_flip($metric->getInsights());

        return array_filter($this->insights, function ($insight) use ($insightsClasses) {
            return array_key_exists(get_class($insight), $insightsClasses);
        });
    }

    /**
     * Returns the quality of the code taking in consideration the current insights.
     *
     * @return float
     */
    public function quality(): float
    {
        $total = count($this->insights);
        $issuesFound = 0;

        foreach ($this->all() as $insight) {
            if ($insight->hasIssue()) {
                $issuesFound++;
            }
        }

        return (bool) $issuesFound ? (($issuesFound * 100.0) / $total) : 100.0;
    }
}
