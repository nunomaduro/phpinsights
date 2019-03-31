<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Metric;
use NunoMaduro\PhpInsights\Domain\Publisher;

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
     * @var \NunoMaduro\PhpInsights\Domain\Publisher
     */
    private $publisher;

    /**
     * Creates a new instance of the feedback.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Insight[]  $insights
     */
    public function __construct(Publisher $publisher, array $insights)
    {
        $this->publisher = $publisher;

        foreach ($insights as $insight) {
            $this->insights[get_class($insight)] = $insight;
        }
    }

    /**
     * @return \NunoMaduro\PhpInsights\Domain\Publisher
     */
    public function getPublisher(): Publisher
    {
        return $this->publisher;
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

        return ($issuesFound * 100.0) / $total;
    }
}
