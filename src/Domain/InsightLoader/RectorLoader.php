<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\InsightLoader;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\Insights\Decorators\RectorDecorator;
use NunoMaduro\PhpInsights\Domain\RectorContainer;
use Rector\Core\Contract\Rector\PhpRectorInterface;

/**
 * @internal
 */
final class RectorLoader implements InsightLoader
{
    /**
     * @var array<string, array|int|string>
     */
    private array $rectorClasses = [];

    /**
     * @var array<RectorDecorator>
     */
    private array $rectors = [];

    private string $dir;

    public function support(string $insightClass): bool
    {
        return array_key_exists(PhpRectorInterface::class, class_implements($insightClass));
    }

    public function load(string $insightClass, string $dir, array $config, Collector $collector): void
    {
        $this->rectorClasses[$insightClass] = $config;
        $this->dir = $dir;
    }

    public function getLoadedInsights(): array
    {
        $rectorClasses = array_keys($this->rectorClasses);

        $rectorContainer = RectorContainer::make($rectorClasses);

        foreach ($rectorClasses as $rectorClass) {
            $rector = $rectorContainer->get($rectorClass);

            $config = $this->rectorClasses[$rectorClass];
            $excludeConfig = $config['exclude'] ?? [];

            $this->rectors[] = new RectorDecorator($rector, $this->dir, $excludeConfig);
        }

        return $this->rectors;
    }
}
