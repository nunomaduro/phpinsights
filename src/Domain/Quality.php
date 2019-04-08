<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Insights\Feedback;

/**
 * @internal
 */
final class Quality
{
    public const VERY_GOOD = 'A';

    public const OK = 'B';

    public const BAD = 'C';

    public const NOT_GOOD = 'D';

    /**
     * Quality constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\Feedback  $feedback
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     */
    public function __construct(Feedback $feedback, Collector $collector)
    {
        $this->collector = $collector;
        $this->feedback = $feedback;
    }

    /**
     * Returns the letter associated with the quality.
     *
     * @return string
     */
    public function getLetter(): string
    {
        $percentage = $this->getPercentage();

        if ($percentage >= 75.0) {
            return self::VERY_GOOD;
        } else if ($percentage >= 50.0) {
            return self::OK;
        } else if ($percentage >= 25.0) {
            return self::BAD;
        }

        return self::NOT_GOOD;
    }

    /**
     * Returns the quality percentage.
     *
     * @return float
     */
    public function getPercentage(): float
    {
        $total = count($insights = $this->feedback->all());
        $issuesNotFound = 0;

        foreach ($insights as $insight) {
            if (! $insight->hasIssue()) {
                $issuesNotFound++;
            }
        }

        return (bool) $issuesNotFound ? (($issuesNotFound * 100.0) / $total) : 100.0;
    }
}
