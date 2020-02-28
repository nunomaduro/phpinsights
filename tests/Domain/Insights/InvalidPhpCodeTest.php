<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\FileProcessors\FixerFileProcessor;
use NunoMaduro\PhpInsights\Domain\Differ;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

final class InvalidPhpCodeTest extends TestCase
{
    public function testNotFailingOnSemiColonAfterExtendClass(): void
    {
        $path = __DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php';

        $file = new SplFileInfo($path, $path, $path);

        $fixerFileProcessor = new FixerFileProcessor(new Differ());

        $fixerFileProcessor->processFile($file);

        self::assertTrue(true);
    }
}
