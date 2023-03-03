<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @see \Tests\Domain\Insights\CyclomaticComplexityIsHighTest
 */
final class CyclomaticComplexityIsHigh extends Insight implements HasDetails, GlobalInsight
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
            'Having `classes` with more than %s cyclomatic complexity is prohibited - Consider refactoring',
            $this->getMaxComplexity()
        );
    }

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
        $complexityLimit = $this->getMaxComplexity();

        $classesComplexity = array_filter(
            $this->collector->getClassComplexity(),
            static fn ($complexity): bool => $complexity > $complexityLimit
        );

        $this->details = array_map(static fn ($class, $complexity): Details => Details::make()
            ->setFile($class)
            ->setMessage("$complexity cyclomatic complexity"), array_keys($classesComplexity), $classesComplexity);
    }

    private function getMaxComplexity(): int
    {
        return (int) ($this->config['maxComplexity'] ?? 5);
    }
}
