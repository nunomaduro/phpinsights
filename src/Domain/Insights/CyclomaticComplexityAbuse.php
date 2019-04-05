<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

/**
 * @internal
 */
final class CyclomaticComplexityAbuse extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return $this->collector->getAverageComplexityPerLogicalLine() > 3.0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return sprintf('Code with cyclomatic complexity bigger then 3.0 is considered hard to maintain.');
    }
}
