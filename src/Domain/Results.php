<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;


/**
 * @internal
 */
final class Results
{
    /**
     * @var array<string, \NunoMaduro\PhpInsights\Domain\Insights\Insight[]>
     */
    private $perCategoryInsights;

    /**
     * Creates a new instance of results.
     *
     * @param  array<string, \NunoMaduro\PhpInsights\Domain\Insights\Insight[]>  $perCategoryInsights
     */
    public function __construct(array $perCategoryInsights)
    {
        $this->perCategoryInsights = $perCategoryInsights;
    }

    /**
     * Gets the code quality.
     *
     * @return float
     */
    public function getCodeQuality(): float
    {
        return $this->getPercentage('Code');
    }

    /**
     * Gets the code quality.
     *
     * @return float
     */
    public function getComplexity(): float
    {
        return $this->getPercentage('Complexity');
    }

    /**
     * Gets the code quality.
     *
     * @return float
     */
    public function getStructure(): float
    {
        return $this->getPercentage('Structure');
    }

    /**
     * Gets the code quality.
     *
     * @return float
     */
    public function getDependencies(): float
    {
        return $this->getPercentage('Dependencies');
    }

    /**
     * Returns the percentage of the given category.
     *
     * @param  string  $category
     *
     * @return float
     */
    private function getPercentage(string $category): float
    {
        $total = count($insights = $this->perCategoryInsights[$category]);
        $issuesNotFound = 0;

        foreach ($insights as $insight) {
            if (! $insight->hasIssue()) {
                $issuesNotFound++;
            }
        }

        $percentage = (bool) $issuesNotFound ? (($issuesNotFound * 100.0) / $total) : 100.0;

        return (float) number_format($percentage, 1, '.', '');
    }
}
