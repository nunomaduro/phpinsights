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
     *
     * @return bool
     */
    public function hasIssue(): bool;

    /**
     * Gets the title of the insight.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the Full Qualified Class Name of Insight used
     *
     * @return string
     */
    public function getInsightClass(): string;
}
