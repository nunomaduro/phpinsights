<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @see \Tests\Domain\Insights\MethodCyclomaticComplexityIsHighTest
 */
final class MethodCyclomaticComplexityIsHigh extends Insight implements HasDetails, GlobalInsight
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
            'Having `methods` with cyclomatic complexity more than %s is prohibited - Consider refactoring',
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
        $complexityLimit = $this->getMaxComplexity();

        $methodComplexity = array_filter(
            $this->collector->getMethodComplexity(),
            static fn ($complexity): bool => $complexity > $complexityLimit
        );

        $this->details = array_map(
            fn ($class, $complexity): Details => $this->getDetailsForClassMethod($class, $complexity),
            array_keys($methodComplexity),
            $methodComplexity
        );
    }

    private function getMaxComplexity(): int
    {
        return (int) ($this->config['maxMethodComplexity'] ?? 5);
    }

    private function getDetailsForClassMethod(string $class, int $complexity): Details
    {
        $file = $class;
        $function = null;
        $colonPosition = strpos($class, ':');

        if ($colonPosition !== false) {
            $file = substr($class, 0, $colonPosition);
            $function = substr($class, $colonPosition + 1);
        }

        $details = Details::make()
            ->setFile($file)
            ->setMessage(sprintf('%d cyclomatic complexity', $complexity));

        if ($function !== null) {
            $details->setFunction($function);
        }

        return $details;
    }
}
