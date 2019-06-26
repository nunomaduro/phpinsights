<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

final class ForbiddenUsingGlobals extends Insight
{
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getGlobalAccesses();
    }

    public function getTitle(): string
    {
        return 'The usage of globals is prohibited - Consider relying in abstractions';
    }
}
