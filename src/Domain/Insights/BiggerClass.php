<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use Symfony\Component\Finder\SplFileInfo;

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
        return (int)$this->publisher->getMaximumClassLength() > 30;
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
        $classLines = $this->collector->getClassLines();

        uasort($classLines, function ($a, $b) {
            return $a + $b;
        });

        $classLines = array_filter($classLines, function ($lines) {
            return $lines > 30;
        });

        $classLines = array_slice($classLines, -3, 3, true);

        return array_map(function ($class, $lines) {
            return "$class <fg=red> --> </> $lines lines";
        }, array_keys($classLines), $classLines);
    }
}
