<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneTraitPerFileSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff;

final class Traits implements HasValue, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', count($collector->getTraits()));
    }

    public function getPercentage(Collector $collector): float
    {
        return $collector->getFiles() !== []
            ? count($collector->getTraits()) / count($collector->getFiles()) * 100
            : 0;
    }

    public function getInsights(): array
    {
        return [
            ForbiddenTraits::class,
            OneTraitPerFileSniff::class,
            SuperfluousTraitNamingSniff::class,
        ];
    }
}
