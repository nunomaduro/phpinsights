<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Insights\Syntax;
use Tests\TestCase;

final class SyntaxTest extends TestCase
{
    public function testHasIssueOnFile(): void
    {
        /** @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insights */
        $insights = $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php'],
            [__DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php']// simulate analyse only this file
        );

        foreach ($insights->all() as $insight) {
            if ($insight instanceof Syntax) {
                self::assertTrue($insight->hasIssue());
                self::assertCount(1, $insight->getDetails());
                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                $detail = $insight->getDetails()[0];
                self::assertSame('PHP syntax error: syntax error, unexpected \';\', expecting \'{\'', $detail->getMessage());
                self::assertSame(2, $detail->getLine());
                self::assertStringContainsString('SemiColonAfterExtendClass.php', $detail->getFile());
            }
        }
    }

    public function testHasIssueOnDirectory(): void
    {
        /** @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insights */
        $insights = $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php'],
            [__DIR__] // simulate analyse only this directory
        );

        foreach ($insights->all() as $insight) {
            if ($insight instanceof Syntax) {
                self::assertTrue($insight->hasIssue());
                self::assertCount(1, $insight->getDetails());
                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                $detail = $insight->getDetails()[0];
                self::assertSame('PHP syntax error: syntax error, unexpected \';\', expecting \'{\'', $detail->getMessage());
                self::assertSame(2, $detail->getLine());
                self::assertStringContainsString('Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php', $detail->getFile());
            }
        }
    }
}
