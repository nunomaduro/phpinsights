<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

/**
 * @internal
 */
final class ForbiddenUsingGlobals extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getGlobalAccesses();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        // @todo add details.
        return $this->collector->getGlobalAccesses();
    }
}
