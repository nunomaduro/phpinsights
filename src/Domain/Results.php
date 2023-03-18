<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Exceptions\InsightClassNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;

/**
 * @internal
 *
 * @see \Tests\Domain\ResultsTest
 */
final class Results
{
    private Collector $collector;

    /**
     * @var array<string, array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>>
     */
    private array $perCategoryInsights;

    /**
     * Creates a new instance of results.
     *
     * @param  array<string, array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight>>  $perCategoryInsights
     */
    public function __construct(Collector $collector, array $perCategoryInsights)
    {
        $this->collector = $collector;
        $this->perCategoryInsights = $perCategoryInsights;
    }

    /**
     * Gets the code quality.
     */
    public function getCodeQuality(): float
    {
        return $this->getPercentage('Code');
    }

    /**
     * Gets the code quality.
     */
    public function getComplexity(): float
    {
        $avg = $this->collector->getAverageComplexityPerMethod() - 1.0;

        return (float) number_format(
            100.0 - max(min($avg * 100.0 / 3.0, 100.0), 0.0),
            1,
            '.',
            ''
        );
    }

    /**
     * Gets the code quality.
     */
    public function getStructure(): float
    {
        return $this->getPercentage('Architecture');
    }

    /**
     * Gets the code quality.
     */
    public function getDependencies(): float
    {
        return $this->getPercentage('Dependencies');
    }

    /**
     * Gets the style quality.
     */
    public function getStyle(): float
    {
        return $this->getPercentage('Style');
    }

    /**
     * Gets number of security issues.
     */
    public function getTotalSecurityIssues(): int
    {
        try {
            /** @var ForbiddenSecurityIssues $insight */
            $insight = $this->getInsightByCategory(ForbiddenSecurityIssues::class, 'Security');

            return count($insight->getDetails());
        } catch (InsightClassNotFound $exception) {
            return 0;
        }
    }

    public function getTotalFix(): int
    {
        $total = 0;
        foreach ($this->perCategoryInsights as $metrics) {
            foreach ($metrics as $insight) {
                if ($insight instanceof Fixable) {
                    $total += $insight->getTotalFix();
                }
            }
        }

        return $total;
    }

    public function getTotalIssues(): int
    {
        $total = 0;
        foreach ($this->perCategoryInsights as $metrics) {
            /** @var Insight $insight */
            foreach ($metrics as $insight) {
                if ($insight->hasIssue()) {
                    if (! $insight instanceof HasDetails) {
                        $total++;

                        continue;
                    }

                    $total += count($insight->getDetails());
                }
            }
        }

        return $total;
    }

    public function hasInsightInCategory(string $insightClass, string $category): bool
    {
        try {
            $this->getInsightByCategory($insightClass, $category);

            return true;
        } catch (InsightClassNotFound $exception) {
            return false;
        }
    }

    /**
     * Returns the percentage of the given category.
     */
    private function getPercentage(string $category): float
    {
        $total = count($insights = $this->perCategoryInsights[$category] ?? []);
        $issuesNotFound = 0;

        foreach ($insights as $insight) {
            if (! $insight->hasIssue()) {
                $issuesNotFound++;
            }
        }

        $percentage = (bool) $issuesNotFound ? $issuesNotFound * 100.0 / $total : 100.0;

        return (float) number_format($percentage, 1, '.', '');
    }

    private function getInsightByCategory(string $insightClass, string $category): Insight
    {
        foreach ($this->perCategoryInsights[$category] ?? [] as $insight) {
            if ($insight instanceof $insightClass) {
                return $insight;
            }
        }

        throw new InsightClassNotFound("{$insightClass} not found in {$category}");
    }
}
