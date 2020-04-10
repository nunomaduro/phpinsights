<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts;

use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
interface FileProcessor
{
    public const FILE_PROCESSOR_TAG = 'phpinsights.file_processor';

    public function processFile(SplFileInfo $splFileInfo): void;

    /**
     * Check if an Insight implementation could be add to current file processor.
     */
    public function support(Insight $insight): bool;

    public function addChecker(Insight $insight): void;
}
