<?php

namespace Tests\Domain\Insights;

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

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenGlobals($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertCount(1, $insight->getDetails());
        self::assertContains('FileWithGlobals.php:3: Usage of super global $_POST found; Usage of GLOBALS are discouraged consider not relying on global scope', $insight->getDetails());
    }

    public function testHasMultipleGlobalsUsage() : void
    {
        $files = [
            __DIR__ . '/Fixtures/FileWithMultipleGlobals.php',
        ];

        $analyzer = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);
        $insight = new ForbiddenGlobals($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertCount(3, $insight->getDetails());
        self::assertContains('FileWithMultipleGlobals.php:3: Usage of "global" keyword found; Usage of GLOBALS are discouraged consider not relying on global scope', $insight->getDetails());
        self::assertNotContains('FileWithGlobals.php:3: Usage of super global $_POST found; Usage of GLOBALS are discouraged consider not relying on global scope', $insight->getDetails());
    }
}
