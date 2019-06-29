<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenGlobals extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool)$this->collector->getGlobalAccesses();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return "{$this->collector->getGlobalAccesses()} globals accesses detected";
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $details = [];
        foreach ($this->collector->getGlobalVariables() as $file => $global) {
            $details[] = "$file: Usage of $global found; Usage of GLOBALS are discouraged consider not relying on global scope";
        }

        return $details;
    }
}
