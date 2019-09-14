<?php

namespace NunoMaduro\PhpInsights\Domain\LinkFormatter;

use NunoMaduro\PhpInsights\Domain\Contracts\FileLinkFormatter;

class NullFileLinkFormatter implements FileLinkFormatter
{
    public function format(string $file, ?int $line): string
    {
        return '';
    }
}
