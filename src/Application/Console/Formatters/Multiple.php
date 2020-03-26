<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;

final class Multiple implements Formatter
{
    /** @var array<Formatter> */
    private $formatters;

    /**
     * @param array<Formatter> $formatters
     */
    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * Format the result to the desired format.
     *
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insightCollection
     * @param array<string> $directories
     * @param array<int, string> $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        array $directories,
        array $metrics
    ): void {
        /** @var Formatter $formatter */
        foreach ($this->formatters as $formatter) {
            $formatter->format($insightCollection, $directories, $metrics);
        }
    }
}
