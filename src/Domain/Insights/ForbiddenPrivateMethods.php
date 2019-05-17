<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

final class ForbiddenPrivateMethods extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getPrivateMethods() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return (string) $this->getConfigByKey('title', 'The use of `private` methods is prohibited');
    }
}
