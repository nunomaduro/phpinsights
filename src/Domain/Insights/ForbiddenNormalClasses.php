<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenNormalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteNonFinalClasses());
    }

    public function getTitle(): string
    {
        return (string) ($this->config['title'] ?? 'Normal classes are forbidden. Classes must be final or abstract');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->collector->getConcreteNonFinalClasses();
    }
}
