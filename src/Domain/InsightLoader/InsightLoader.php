<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader as LoaderContract;

/**
 * @internal
 */
final class InsightLoader implements LoaderContract
{
    /**
     * @var InsightContract[]
     */
    private array $insights = [];

    public function support(string $insightClass): bool
    {
        return array_key_exists(InsightContract::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): void
    {
        $this->insights[] = new $insightClass($collector, $config);
    }

    public function getLoadedInsights(): array
    {
        return $this->insights;
    }
}
