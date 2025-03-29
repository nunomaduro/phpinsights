<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\MethodCyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use Tests\TestCase;

final class MethodCyclomaticComplexityIsHighTest extends TestCase
{
    public function testMethodHasNoCyclomaticComplexity(): void
    {
        $path = __DIR__ . '/Fixtures/';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new MethodCyclomaticComplexityIsHigh($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertCount(0, $insight->getDetails());
    }

    public function testMethodHasToMuchCyclomaticComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/ClassWithComplexMethod.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new MethodCyclomaticComplexityIsHigh($collector, []);
        $insight->process();

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(1, $insight->getDetails());

        $message = $insight->getDetails()[0]->getMessage() ?? '';
        $file = $insight->getDetails()[0]->getFile() ?? '';
        $function = $insight->getDetails()[0]->getFunction() ?? '';

        self::assertStringContainsString('ClassWithComplexMethod.php', $file);
        self::assertSame('first', $function);
        self::assertSame('6 cyclomatic complexity', $message);
    }

    public function testMethodWeCanConfigureTheMaxComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/ClassWithComplexMethod.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new MethodCyclomaticComplexityIsHigh($collector, ['maxMethodComplexity' => 10]);
        $insight->process();

        self::assertFalse($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(0, $insight->getDetails());
    }

    public function testItNoReturnIssueWhenFileExcluded(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/ClassWithComplexMethod.php';
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    MethodCyclomaticComplexityIsHigh::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        foreach ($collection->allFrom(new Complexity()) as $insight) {
            if ($insight->getInsightClass() === MethodCyclomaticComplexityIsHigh::class) {
                self::assertFalse($insight->hasIssue());
            }
        }
    }
}
