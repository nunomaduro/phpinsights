<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use Tests\TestCase;

final class CyclomaticComplexityIsHighTest extends TestCase
{
    public function testClassHasNoCyclomaticComplexity(): void
    {
        $path = __DIR__ . '/Fixtures/';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new CyclomaticComplexityIsHigh($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertCount(0, $insight->getDetails());
    }

    public function testClassHasToMuchCyclomaticComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/LittleToComplexClass.php',
            __DIR__ . '/Fixtures/VeryMuchToComplexClass.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new CyclomaticComplexityIsHigh($collector, []);
        $insight->process();

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(2, $insight->getDetails());

        $messages = [];
        $files = [];
        /** @var Details $detail */
        foreach ($insight->getDetails() as $detail) {
            $messages[] = $detail->getMessage();
            $files[] = PathShortener::fileName($detail, $commonPath);
        }

        self::assertContains('LittleToComplexClass.php', $files);
        self::assertContains('6 cyclomatic complexity', $messages);

        self::assertContains('VeryMuchToComplexClass.php', $files);
        self::assertContains('13 cyclomatic complexity', $messages);
    }

    public function testClassWeCanConfigureTheMaxComplexity(): void
    {
        $files = [
            __DIR__ . '/Fixtures/LittleToComplexClass.php',
            __DIR__ . '/Fixtures/VeryMuchToComplexClass.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse([__DIR__ . '/Fixtures/'], $files, $commonPath);
        $insight = new CyclomaticComplexityIsHigh($collector, ['maxComplexity' => 10]);
        $insight->process();

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(1, $insight->getDetails());

        $messages = [];
        $files = [];
        /** @var Details $detail */
        foreach ($insight->getDetails() as $detail) {
            $messages[] = $detail->getMessage();
            $files[] = PathShortener::fileName($detail, $commonPath);
        }

        self::assertNotContains('LittleToComplexClass.php', $files);
        self::assertNotContains('6 cyclomatic complexity', $messages);

        self::assertContains('VeryMuchToComplexClass.php', $files);
        self::assertContains('13 cyclomatic complexity', $messages);
    }

    public function testItNoReturnIssueWhenFileExcluded(): void
    {
        $fileLocation = __DIR__ . '/Fixtures/VeryMuchToComplexClass.php';
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    CyclomaticComplexityIsHigh::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        foreach ($collection->allFrom(new Complexity()) as $insight) {
            if ($insight->getInsightClass() === CyclomaticComplexityIsHigh::class) {
                self::assertFalse($insight->hasIssue());
            }
        }
    }
}
