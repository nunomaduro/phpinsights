<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use Tests\TestCase;

final class ForbiddenFinalClassTest extends TestCase
{
    public function testHasIssue(): void
    {
        $files = [
            __DIR__ . '/Fixtures/ForbiddenFinalClass.php',
        ];

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenFinalClasses($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertNotEmpty($insight->getDetails());
    }

    public function testSkipFile(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/ForbiddenFinalClass.php';

        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    ForbiddenFinalClasses::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        $classErrors = 0;

        foreach ($collection->allFrom(new Classes()) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === ForbiddenFinalClasses::class
            ) {
                $classErrors++;
            }
        }

        self::assertEquals(1, $classErrors);
    }
}
