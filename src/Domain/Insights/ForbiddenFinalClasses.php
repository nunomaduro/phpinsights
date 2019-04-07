<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

/**
 * @internal
 */
final class ForbiddenFinalClasses extends Insight
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
        return (string) $this->config['title'] ?? 'The use of `final` classes is prohibited';
    }
}
