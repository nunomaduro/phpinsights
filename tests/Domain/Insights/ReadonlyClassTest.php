<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use Tests\TestCase;

final class ReadonlyClassTest extends TestCase
{
    public function testHasIssue(): void
    {
        if (PHP_VERSION_ID < 80200) {
            self::markTestSkipped('Readonly classes are only available in PHP 8.2+');
        }

        $files = [
            __DIR__ . '/Fixtures/ReadonlyClass.php',
        ];

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, PathShortener::extractCommonPath($files));
        $insight = new ForbiddenNormalClasses($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertNotEmpty($insight->getDetails());
    }

    public function testSkipFile(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/ReadonlyClass.php';

        $collection = $this->runAnalyserOnConfig(
            [
                'add' => [
                    Classes::class => [
                        ForbiddenNormalClasses::class,
                    ],
                ],
                'config' => [
                    ForbiddenNormalClasses::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        $classErrors = 0;

        foreach ($collection->allFrom(new Classes()) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === ForbiddenNormalClasses::class) {
                $classErrors++;
            }
        }

        self::assertEquals(0, $classErrors);
    }
}
