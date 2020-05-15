<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\Decorators\SniffDecorator;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;

/**
 * @internal
 */
final class SniffLoader implements InsightLoader
{
    /**
     * @var array<InsightContract>
     */
    private array $sniffs = [];

    public function support(string $insightClass): bool
    {
        return array_key_exists(SniffContract::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): void
    {
        /** @var SniffContract $sniff */
        $sniff = new $insightClass();

        foreach ($config as $property => $value) {
            $sniff->{$property} = $value;
        }

        $this->sniffs[] = new SniffDecorator($sniff, $dir);
    }

    public function getLoadedInsights(): array
    {
        return $this->sniffs;
    }
}
