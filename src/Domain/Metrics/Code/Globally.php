<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Code;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff;

final class Globally implements HasValue, HasPercentage, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getNotInClassesOrFunctions());
    }

    public function getPercentage(Collector $collector): float
    {
        return $collector->getLines() > 0 ? ($collector->getNotInClassesOrFunctions() / $collector->getLines()) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            GlobalKeywordSniff::class,
            ForbiddenGlobals::class,
        ];
    }
}
