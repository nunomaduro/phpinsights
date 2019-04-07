<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class BiggerClass extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return $this->collector->getMaximumClassLength() > 30;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Having `classes` with more than 30 lines is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $classLines = $this->collector->getPerClassLines();

        uasort($classLines, function ($a, $b) {
            return $b - $a;
        });

        $classLines = array_reverse($classLines);

        return array_map(function ($class, $lines) {
            return "$class: $lines lines";
        }, array_keys($classLines), $classLines);
    }
}
