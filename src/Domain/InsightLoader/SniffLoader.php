<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;

/**
 * @internal
 */
final class SniffLoader implements InsightLoader
{
    public function support(string $insightClass): bool
    {
        return array_key_exists(SniffContract::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): Insight
    {
        /** @var SniffContract $sniff */
        $sniff = new $insightClass();

        foreach ($config as $property => $value) {
            $sniff->{$property} = $value;
        }

        return new SniffDecorator(
            $sniff,
            $dir
        );
    }
}
