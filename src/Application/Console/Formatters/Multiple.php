<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;

final class Multiple implements Formatter
{
    /** @var array<Formatter> */
    private $formatters;

    public function __construct(array $formatters) {
        $this->formatters = $formatters;
    }

    /**
     * @inheritDoc
     */
    public function format(InsightCollection $insightCollection, string $dir, array $metrics): void
    {
        /** @var Formatter $formatter */
        foreach ($this->formatters as $formatter) {
            $formatter->format($insightCollection, $dir, $metrics);
        }
    }
}
