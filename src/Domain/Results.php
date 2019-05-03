<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

/**
 * @internal
 */
final class Results
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    private $collector;

    /**
     * @var array<string, array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>>
     */
    private $perCategoryInsights;

    /**
     * Creates a new instance of results.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Collector                               $collector
     * @param array<string, array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>> $perCategoryInsights
     */
    public function __construct(\NunoMaduro\PhpInsights\Domain\Collector $collector, array $perCategoryInsights)
    {
        $this->collector = $collector;
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
        $avg = $this->collector->getAverageComplexityPerMethod() - 1.0;

        return (float) number_format(
            100.0 - min(($avg * 100.0) / 3.0, 100.0),
            1,
            '.',
            ''
        );
    }

    /**
     * Gets the code quality.
     *
     * @return float
     */
    public function getStructure(): float
    {
        return $this->getPercentage('Architecture');
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
     * Gets the style quality.
     *
     * @return float
     */
    public function getStyle(): float
    {
        return $this->getPercentage('Style');
    }

    /**
     * Returns the percentage of the given category.
     *
     * @param string $category
     *
     * @return float
     */
    private function getPercentage(string $category): float
    {
        $total = count($insights = $this->perCategoryInsights[$category]);
        $issuesNotFound = 0;

        foreach ($insights as $insight) {
            if (!$insight->hasIssue()) {
                ++$issuesNotFound;
            }
        }

        $percentage = (bool) $issuesNotFound ? (($issuesNotFound * 100.0) / $total) : 100.0;

        return (float) number_format($percentage, 1, '.', '');
    }
}
