<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use PHPUnit\Framework\TestCase;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals;
use NunoMaduro\PhpInsights\Domain\Analyser;

final class ForbiddenGlobalsTest extends TestCase
{
    public function testFileHasNoGlobals() : void
    {
        $files = [
            __DIR__ . '/Fixtures/LittleToComplexClass.php',
            __DIR__ . '/Fixtures/VeryMuchToComplexClass.php',
        ];

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenGlobals($collector, []);

        self::assertFalse($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertEmpty($insight->getDetails());
    }

    public function testHasOneGlobalUsage() : void
    {
        $files = [
            __DIR__ . '/Fixtures/FileWithGlobals.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenGlobals($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertCount(1, $insight->getDetails());

        $detail = $insight->getDetails()[0];

        $message = $detail->getMessage();
        self::assertEquals('Usage of super global $_POST found; Usage of GLOBALS are discouraged consider not relying on global scope', $message);

        $file = PathShortener::fileName($detail, $commonPath);
        self::assertEquals('FileWithGlobals.php:3', $file);
    }

    public function testHasMultipleGlobalsUsage() : void
    {
        $files = [
            __DIR__ . '/Fixtures/FileWithMultipleGlobals.php',
        ];

        $commonPath = PathShortener::extractCommonPath($files);

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenGlobals($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertCount(3, $insight->getDetails());
        $detail = $insight->getDetails()[0];

        $message = $detail->getMessage();
        self::assertEquals('Usage of "global" keyword found; Usage of GLOBALS are discouraged consider not relying on global scope', $message);

        $file = PathShortener::fileName($detail, $commonPath);
        self::assertEquals('FileWithMultipleGlobals.php:3', $file);
    }
}
