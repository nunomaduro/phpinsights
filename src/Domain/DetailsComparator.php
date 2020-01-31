<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

/**
 * @internal
 */
final class DetailsComparator
{
    public function __invoke(Details $first, Details $second): int
    {
        $comparisons = [
            $this->fileComparison($first, $second),
            $this->lineComparison($first, $second),
            $this->functionComparison($first, $second),
            $this->messageComparison($first, $second),
        ];

        foreach ($comparisons as $comparison) {
            if ($comparison !== 0) {
                return $comparison;
            }
        }

        return 0;
    }

    private function fileComparison(Details $first, Details $second): int
    {
        return ($first->hasFile() ? $first->getFile() : null) <=> ($second->hasFile() ? $second->getFile() : null);
    }

    private function lineComparison(Details $first, Details $second): int
    {
        return ($first->hasLine() ? $first->getLine() : null) <=> ($second->hasLine() ? $second->getLine() : null);
    }

    private function functionComparison(Details $first, Details $second): int
    {
        return ($first->hasFunction() ? $first->getFunction() : null) <=> ($second->hasFunction() ? $second->getFunction() : null);
    }

    private function messageComparison(Details $first, Details $second): int
    {
        return ($first->hasMessage() ? $first->getMessage() : null) <=> ($second->hasMessage() ? $second->getMessage() : null);
    }
}
