<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use Tests\TestCase;

final class EmptyNamespaceTest extends TestCase
{
    public function testNotFailingOnAnalyzingEmptyNamespaceFile(): void
    {
        $files = [
            __DIR__ . '/Fixtures/EmptyNamespaceFile.php',
        ];

        $analyzer  = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);

        self::assertNotEmpty($collector);
    }
}
