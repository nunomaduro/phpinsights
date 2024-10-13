<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ClassMethodAverageCyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use Tests\TestCase;

final class ClassMethodAverageCyclomaticComplexityIsHighTest extends TestCase
{
    public function testMethodHasNoCyclomaticComplexity(): void
    {
        $path = __DIR__ . '/Fixtures/';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ClassMethodAverageCyclomaticComplexityIsHigh($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertCount(0, $insight->getDetails());
    }

    public function testMethodHasToMuchCyclomaticComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/ClassWithHighMethodAverageComplexity.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new ClassMethodAverageCyclomaticComplexityIsHigh($collector, []);
        $insight->process();

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(1, $insight->getDetails());

        $message = $insight->getDetails()[0]->getMessage() ?? '';
        $file = $insight->getDetails()[0]->getFile() ?? '';

        self::assertStringContainsString('ClassWithHighMethodAverageComplexity.php', $file);
        self::assertSame('5.33 cyclomatic complexity', $message);
    }

    public function testMethodWeCanConfigureTheMaxComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/ClassWithHighMethodAverageComplexity.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new ClassMethodAverageCyclomaticComplexityIsHigh($collector, ['maxClassMethodAverageComplexity' => 10]);
        $insight->process();

        self::assertFalse($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(0, $insight->getDetails());
    }

    public function testItNoReturnIssueWhenFileExcluded(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/ClassWithHighMethodAverageComplexity.php';
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    ClassMethodAverageCyclomaticComplexityIsHigh::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        foreach ($collection->allFrom(new Complexity()) as $insight) {
            if ($insight->getInsightClass() === ClassMethodAverageCyclomaticComplexityIsHigh::class) {
                self::assertFalse($insight->hasIssue());
            }
        }
    }
}
