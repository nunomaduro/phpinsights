<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use Tests\TestCase;

final class MultiplePathTest extends TestCase
{
    public function testAnalyserCanWorkOnMultiplePath(): void
    {
        $path1 = __DIR__ . '/Fixtures/MultiplePath/FolderA/FileToInspect.php';
        $path2 = __DIR__ . '/Fixtures/MultiplePath/FolderB/AnotherFileToInspect.php';

        $collection = $this->runAnalyserOnPreset(
            'default',
            [$path1, $path2],
            [
                $path1,
                $path2,
            ]
        );

        self::assertNotEmpty($collection->results());
    }
}
