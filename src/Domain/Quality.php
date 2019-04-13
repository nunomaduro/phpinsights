<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;

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
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection
     */
    private $insightCollection;

    /**
     * Quality constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     */
    public function __construct(InsightCollection $insightCollection)
    {
        $this->insightCollection = $insightCollection;
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
        $total = count($insights = $this->insightCollection->all());
        $issuesNotFound = 0;

        foreach ($insights as $insight) {
            if (! $insight->hasIssue()) {
                $issuesNotFound++;
            }
        }

        return (bool) $issuesNotFound ? (($issuesNotFound * 100.0) / $total) : 100.0;
    }
}
