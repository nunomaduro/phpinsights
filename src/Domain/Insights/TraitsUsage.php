<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class TraitsUsage extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (int) count($this->collector->getTraits()) > 0;
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
