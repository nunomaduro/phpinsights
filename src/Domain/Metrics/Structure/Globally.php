<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;

/**
 * @internal
 */
final class Globally implements HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return count($collector->getFiles()) - $collector->getClasses() - $collector->getInterfaces() - count($collector->getTraits());
    }
}
