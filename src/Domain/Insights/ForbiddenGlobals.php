<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

final class ForbiddenGlobals extends Insight
{
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getGlobalAccesses();
    }

    public function getTitle(): string
    {
        return "{$this->collector->getGlobalAccesses()} globals accesses detected";
    }
}
