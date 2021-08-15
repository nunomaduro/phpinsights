<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;

final class Globally implements HasPercentage
{
    public function getPercentage(Collector $collector): float
    {
        $value = count($collector->getFiles())
            - $collector->getClasses()
            - $collector->getInterfaces()
            - count($collector->getTraits());

        return $collector->getFiles() !== [] ? $value / count($collector->getFiles()) * 100 : 0;
    }
}
