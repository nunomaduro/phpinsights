<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use Symfony\Component\Finder\SplFileInfo;

interface FileProcessor
{
    public function processFile(SplFileInfo $splFileInfo): void;

    public function support(Insight $insight): bool;

    public function addChecker(Insight $insight): void;
}
