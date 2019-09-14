<?php


namespace NunoMaduro\PhpInsights\Domain\Contracts;


interface FileLinkFormatter
{
    public function format(string $file, ?int $line): string;
}
