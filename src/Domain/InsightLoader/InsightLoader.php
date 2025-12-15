<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader as LoaderContract;

/**
 * @internal
 */
final class InsightLoader implements LoaderContract
{
    public function support(string $insightClass): bool
    {
        $interfaces = class_implements($insightClass);
        if (!is_array($interfaces)) {
            return false;
        }
        return array_key_exists(Insight::class, $interfaces);
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): Insight
    {
        return new $insightClass($collector, $config);
    }
}
