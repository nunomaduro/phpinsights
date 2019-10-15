<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinkFormatter;

use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter;

/**
 * @internal
 */
final class NullFileLinkFormatter implements FileLinkFormatter
{
    public function format(string $file, int $line): string
    {
        return '';
    }
}
