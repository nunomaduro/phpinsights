<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneInterfacePerFileSniff;

final class Interfaces implements HasValue, HasPercentage, HasInsights
{
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', $collector->getInterfaces());
    }

    public function getPercentage(Collector $collector): float
    {
        return count($collector->getFiles()) > 0 ? ($collector->getInterfaces() / count($collector->getFiles())) * 100 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            OneInterfacePerFileSniff::class,
        ];
    }
}
