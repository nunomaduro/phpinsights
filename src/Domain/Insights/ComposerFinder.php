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
        foreach ($collector->getFiles() as $file) {
            if ($file->getFilename() === 'composer.json') {
                return file_get_contents($file->getRealPath());
            }
        }

        throw new RuntimeException('`composer.json` not found.');
    }
}
