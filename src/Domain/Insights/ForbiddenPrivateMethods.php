<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

/**
 * @internal
 */
final class ForbiddenPrivateMethods extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getPrivateMethods();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->config['title'] ?? 'The use of `private` methods is prohibited';
    }
}
