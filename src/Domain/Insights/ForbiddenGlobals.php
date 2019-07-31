<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenGlobals extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return (bool) $this->collector->getGlobalAccesses();
    }

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
        foreach ($this->collector->getGlobalVariableAccesses() as $file => $global) {
            $details[] = "$file: Usage of $global found; Usage of GLOBALS are discouraged consider not relying on global scope";
        }

        return $details;
    }
}
