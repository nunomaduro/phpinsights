<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @see \Tests\Domain\Insights\CyclomaticComplexityIsHighTest
 */
final class CyclomaticComplexityIsHigh extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        $maxComplexity = $this->getMaxComplexity();

        foreach ($this->collector->getClassComplexity() as $complexity) {
            if ($complexity > $maxComplexity) {
                return true;
            }
        }

        return false;
    }

    public function getTitle(): string
    {
        return sprintf(
            'Having `classes` with more than %s cyclomatic complexity is prohibited - Consider refactoring',
            $this->getMaxComplexity()
        );
    }

    public function getDetails(): array
    {
        $complexityLimit = $this->getMaxComplexity();

        $classesComplexity = array_filter(
            $this->collector->getClassComplexity(),
            static fn ($complexity): bool => $complexity > $complexityLimit
        );

        uasort($classesComplexity, static fn ($left, $right) => $right - $left);

        $classesComplexity = array_reverse(
            $this->filterFilesWithoutExcluded($classesComplexity)
        );

        return array_map(static fn ($class, $complexity): Details => Details::make()
            ->setFile($class)
            ->setMessage("${complexity} cyclomatic complexity"), array_keys($classesComplexity), $classesComplexity);
    }

    private function getMaxComplexity(): int
    {
        return (int) ($this->config['maxComplexity'] ?? 5);
    }
}
