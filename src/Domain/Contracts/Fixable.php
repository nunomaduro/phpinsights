<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface Fixable extends Insight
{
    public function getTotalFix(): int;

    /**
     * @return array<\NunoMaduro\PhpInsights\Domain\Details>
     */
    public function getFixPerFile(): array;
}
