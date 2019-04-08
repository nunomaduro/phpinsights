<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class MethodTooBig extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return $this->collector->getMaximumMethodLength() > 25;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Having `methods` with more than 25 lines is prohibited - Consider refactoring';
    }


    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $methodLines = array_filter($this->collector->getMethodLines(), function ($lines) {
            return $lines > 25;
        });

        uasort($methodLines, function ($a, $b) {
            return $b - $a;
        });

        $methodLines = array_reverse($methodLines);

        return array_map(function ($class, $lines) {
            return "$class: $lines lines";
        }, array_keys($methodLines), $methodLines);
    }
}
