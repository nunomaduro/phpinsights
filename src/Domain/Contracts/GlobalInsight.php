<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface GlobalInsight extends Insight
{
    /**
     * Launch Insight inspection.
     */
    public function process(): void;
}
