<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenFinalClasses extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteFinalClasses());
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return (string) $this->getConfigByKey('title', 'The use of `final` classes is prohibited');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->collector->getConcreteFinalClasses();
    }
}
