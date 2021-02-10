<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\Decorators\FixerDecorator;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;

/**
 * @internal
 */
final class FixerLoader implements InsightLoader
{
    /**
     * @var array<InsightContract>
     */
    private array $fixers = [];

    public function support(string $insightClass): bool
    {
        return array_key_exists(FixerInterface::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): void
    {
        $fixer = new $insightClass();

        $excludeConfig = [];

        if (isset($config['exclude'])) {
            /** @var array<string> $excludeConfig */
            $excludeConfig = $config['exclude'];
            unset($config['exclude']);
        }

        if ($fixer instanceof ConfigurableFixerInterface && count($config) > 0) {
            $fixer->configure($config);
        }

        $this->fixers[] = new FixerDecorator($fixer, $dir, $excludeConfig);
    }

    public function getLoadedInsights(): array
    {
        return $this->fixers;
    }
}
