<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeFileRepository;

final class CyclomaticComplexityIsHighTest extends TestCase
{
    public function testClassHasNoCyclomaticComplexity(): void
    {
        $collector = new Collector(__DIR__ . '/Fixtures/');
        $insight = new CyclomaticComplexityIsHigh($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
    }

    public function testClassHasToMuchCyclomaticComplexity(): void
    {
        $analyser = new Analyser();

        $files = [
            __DIR__ . '/Fixtures/LittleToComplexClass.php',
            __DIR__ . '/Fixtures/VeryMuchToComplexClass.php',
        ];

        $fileRepository = new FakeFileRepository($files);

        $insightCollectionFactory = new InsightCollectionFactory(
            $fileRepository,
            $analyser
        );
        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new CyclomaticComplexityIsHigh($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(2, $insight->getDetails());
        self::assertContains('LittleToComplexClass.php: 6 cyclomatic complexity', $insight->getDetails());
        self::assertContains('VeryMuchToComplexClass.php: 13 cyclomatic complexity', $insight->getDetails());
    }

    public function testClassWeCanConfigureTheMaxComplexity(): void
    {
        $analyser = new Analyser();

        $files = [
            __DIR__ . '/Fixtures/LittleToComplexClass.php',
            __DIR__ . '/Fixtures/VeryMuchToComplexClass.php',
        ];

        $fileRepository = new FakeFileRepository($files);

        $insightCollectionFactory = new InsightCollectionFactory(
            $fileRepository,
            $analyser
        );
        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new CyclomaticComplexityIsHigh($collector, ['maxComplexity' => 10]);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertCount(1, $insight->getDetails());
        self::assertNotContains('LittleToComplexClass.php: 6 cyclomatic complexity', $insight->getDetails());
        self::assertContains('VeryMuchToComplexClass.php: 13 cyclomatic complexity', $insight->getDetails());
    }
}
