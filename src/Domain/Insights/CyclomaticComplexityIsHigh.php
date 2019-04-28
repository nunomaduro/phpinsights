<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class CyclomaticComplexityIsHigh extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        foreach ($this->collector->getClassComplexity() as $complexity) {
            if ($complexity > 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return sprintf('Having `classes` with more than 5 cyclomatic complexity is prohibited - Consider refactoring');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $classesComplexity = array_filter($this->collector->getClassComplexity(), function ($complexity) {
            return $complexity > 5;
        });

        uasort($classesComplexity, function ($a, $b) {
            return $b - $a;
        });

        $classesComplexity = array_reverse($classesComplexity);

        return array_map(function ($class, $complexity) {
            return "$class: $complexity cyclomatic complexity";
        }, array_keys($classesComplexity), $classesComplexity);
    }
}
