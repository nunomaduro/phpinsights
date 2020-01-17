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
            SniffLoader::class => static function (): SniffLoader {
                return new SniffLoader();
            },
            FixerLoader::class => static function (): FixerLoader {
                return new FixerLoader();
            },
        ];
    }
}
