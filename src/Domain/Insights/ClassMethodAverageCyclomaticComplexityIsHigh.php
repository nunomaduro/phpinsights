<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @see \Tests\Domain\Insights\ClassMethodAverageCyclomaticComplexityIsHighTest
 */
final class ClassMethodAverageCyclomaticComplexityIsHigh extends Insight implements HasDetails, GlobalInsight
{
    /**
     * @var array<Details>
     */
    private array $details = [];

    public function hasIssue(): bool
    {
        return $this->details !== [];
    }

    public function getTitle(): string
    {
        return sprintf(
            'Having `classes` with average method cyclomatic complexity more than %s is prohibited - Consider refactoring',
            $this->getMaxComplexity()
        );
    }

    /**
     * @return array<int, Details>
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public function process(): void
    {
        // Exclude in collector all excluded files
        if ($this->excludedFiles !== []) {
            $this->collector->excludeComplexityFiles($this->excludedFiles);
        }

        $averageClassComplexity = $this->getAverageClassComplexity();

        // Exclude the ones which didn't pass the threshold
        $complexityLimit = $this->getMaxComplexity();
        $averageClassComplexity = array_filter(
            $averageClassComplexity,
            static fn ($complexity): bool => $complexity > $complexityLimit
        );

        $this->details = array_map(
            static fn ($class, $complexity): Details => Details::make()
                ->setFile($class)
                ->setMessage(sprintf('%.2f cyclomatic complexity', $complexity)),
            array_keys($averageClassComplexity),
            $averageClassComplexity
        );
    }

    private function getMaxComplexity(): float
    {
        return (float) ($this->config['maxClassMethodAverageComplexity'] ?? 5.0);
    }

    private function getFile(string $classMethod): string
    {
        $colonPosition = strpos($classMethod, ':');

        if ($colonPosition !== false) {
            return substr($classMethod, 0, $colonPosition);
        }

        return $classMethod;
    }

    /**
     * @return array<string, float>
     */
    private function getAverageClassComplexity(): array
    {
        // Group method complexities by files
        $classComplexities = [];

        foreach ($this->collector->getMethodComplexity() as $classMethod => $complexity) {
            $classComplexities[$this->getFile($classMethod)][] = $complexity;
        }

        // Calculate average complexity of each file
        $averageClassComplexity = [];

        foreach ($classComplexities as $file => $complexities) {
            $averageClassComplexity[$file] = array_sum($complexities) / count($complexities);
        }

        return $averageClassComplexity;
    }
}
