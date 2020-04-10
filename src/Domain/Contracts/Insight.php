<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface Insight
{
    /**
     * Checks if the insight detects an issue.
     */
    public function hasIssue(): bool;

    /**
     * Gets the title of the insight.
     */
    public function getTitle(): string;

    /**
     * Get the class name of Insight used.
     */
    public function getInsightClass(): string;
}
