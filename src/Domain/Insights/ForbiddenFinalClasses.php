<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenFinalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteFinalClasses());
    }

    public function getTitle(): string
    {
        return array_key_exists('title', $this->config) ? (string) $this->config['title'] : 'The use of `final` classes is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->collector->getConcreteFinalClasses();
    }
}
