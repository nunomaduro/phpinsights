<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class ForbiddenFunctions extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return count($this->collector->getGlobalFunctions()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Using global helpers is prohibited';
    }

    public function getDetails(): array
    {
        $globalFunctions = $this->collector->getGlobalFunctions();

        return array_map(function ($file, $function) {
            return "$file - $function";
        }, array_keys($globalFunctions), $globalFunctions);
    }
}
