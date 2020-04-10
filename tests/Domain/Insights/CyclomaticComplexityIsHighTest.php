<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use PHPUnit\Framework\TestCase;

final class CyclomaticComplexityIsHighTest extends TestCase
{
    public function testClassHasNoCyclomaticComplexity(): void
    {
        $path = __DIR__ . '/Fixtures/';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new CyclomaticComplexityIsHigh($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
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
}
