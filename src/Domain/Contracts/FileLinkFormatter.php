<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

/**
 * @internal
 */
interface FileLinkFormatter
{
    public function format(string $file, int $line): string;
}
