<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

/**
 * @internal
 */
final class ForbiddenGlobals extends Insight
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
        return "{$this->collector->getGlobalVariableAccesses()} globals accesses detected";
    }
}
