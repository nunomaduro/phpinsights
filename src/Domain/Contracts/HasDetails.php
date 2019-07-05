<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface HasDetails extends Insight
{
    /**
     * Returns the details of the insight.
     *
     * @return array<string>
     */
    public function getDetails(): array;
}
