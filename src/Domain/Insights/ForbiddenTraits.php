<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenTraits extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return count($this->collector->getTraits()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'The use of `traits` is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->collector->getTraits();
    }
}
