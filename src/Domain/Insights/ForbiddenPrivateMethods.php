<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

final class ForbiddenPrivateMethods extends Insight
{
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getPrivateMethods() > 0;
    }

    public function getTitle(): string
    {
        return (string) ($this->config['title'] ?? 'The use of `private` methods is prohibited');
    }
}
