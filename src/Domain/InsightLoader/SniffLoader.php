<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;

/**
 * @internal
 */
final class SniffLoader implements InsightLoader
{
    /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight[] */
    private $loaded = [];

    public function support(string $insightClass): bool
    {
        return array_key_exists(SniffContract::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config): void
    {
        /** @var SniffContract $sniff */
        $sniff = new $insightClass();

        foreach ($config as $property => $value) {
            $sniff->{$property} = $value;
        }

        $this->loaded[] = new SniffDecorator(
            $sniff,
            $dir
        );
    }

    public function getLoadedInsights(): array
    {
        return $this->loaded;
    }
}
