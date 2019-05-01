<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

final class ForbiddenNormalClasses extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteNonFinalClasses());
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return (string) ($this->config['title'] ?? 'Normal classes are forbidden. Classes must be final or abstract.');
    }
}
