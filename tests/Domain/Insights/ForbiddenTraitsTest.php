<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits;
use Tests\TestCase;

final class ForbiddenTraitsTest extends TestCase
{
    public function testHasIssue(): void
    {
        $files = [
            __DIR__ . '/Fixtures/MyTrait.php',
        ];

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, PathShortener::extractCommonPath($files));
        $insight = new ForbiddenTraits($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertNotEmpty($insight->getDetails());
    }

    public function testSkipFile(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/MyTrait.php';

        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    ForbiddenTraits::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        $traitErrors = 0;

        foreach ($collection->allFrom(new Traits()) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === ForbiddenTraits::class) {
                $traitErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $traitErrors);
    }
}
