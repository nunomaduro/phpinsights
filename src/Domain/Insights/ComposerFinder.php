<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use RuntimeException;

/**
 * @internal
 */
final class ComposerFinder
{
    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Collector  $collector
     *
     * @return string
     */
    public static function contents(Collector $collector): string
    {
        if (file_exists($collector->getDir() . '/composer.json')) {
            return (string) file_get_contents($collector->getDir() . '/composer.json');
        }

        throw new RuntimeException('`composer.json` not found.');
    }
}
