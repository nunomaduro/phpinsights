<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class SourceCodeMethods implements HasAvg, HasMax, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getAvg(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getAverageMethodLength());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        // @todo
        return 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMax(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getMaximumMethodLength());
    }
}
