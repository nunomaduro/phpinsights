<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\InsightLoader\FixerLoader;
use NunoMaduro\PhpInsights\Domain\InsightLoader\SniffLoader;

/**
 * @internal
 */
final class InsightLoaders
{
    /**
     * Injects Insight Loader into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            SniffLoader::class => static fn (): SniffLoader => new SniffLoader(),
            FixerLoader::class => static fn (): FixerLoader => new FixerLoader(),
        ];
    }
}
