<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\SyntaxCheck;
use Tests\TestCase;

final class SyntaxCheckTest extends TestCase
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
            if ($insight instanceof SyntaxCheck) {
                self::assertTrue($insight->hasIssue());
                self::assertCount(1, $insight->getDetails());
                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                $detail = $insight->getDetails()[0];
                self::assertStringContainsString('PHP syntax error: syntax error, unexpected', $detail->getMessage());
                self::assertSame(2, $detail->getLine());
                self::assertStringContainsString('SemiColonAfterExtendClass.php', $detail->getFile());
            }
        }
    }

    public function testHasIssueOnDirectory(): void
    {
        $basepath = __DIR__ . '/Fixtures/InvalidPhpCode';

        /** @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insights */
        $insights = $this->runAnalyserOnPreset(
            'default',
            [$basepath . '/SemiColonAfterExtendClass.php', $basepath . '/UnclosedComment.php'],
            [__DIR__] // simulate analyse only this directory
        );

        foreach ($insights->all() as $insight) {
            if ($insight instanceof SyntaxCheck) {
                $details = $insight->getDetails();
                usort($details, static fn (Details $a, Details $b): int => $a->getFile() <=> $b->getFile());

                self::assertTrue($insight->hasIssue());
                if (PHP_MAJOR_VERSION === 7) {
                    self::assertCount(2, $details);
                } else {
                    self::assertCount(3, $details);
                }

                /** @var \NunoMaduro\PhpInsights\Domain\Details $detail */
                $detail = $details[0];
                self::assertStringContainsString('PHP syntax error: syntax error, unexpected', $detail->getMessage());
                self::assertSame(2, $detail->getLine());
                self::assertStringContainsString('Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php', $detail->getFile());
            }
        }
    }

    public function testExcludesPathsFromPreset(): void
    {
        $basepath = __DIR__ . '/Fixtures/InvalidPhpCode';

        /** @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insights */
        $insights = $this->runAnalyserOnPreset(
            'laravel',
            [$basepath . '/_ide_helper.php', $basepath . '/UnclosedComment.php'],
            [$basepath] // simulate analysing a project directory
        );

        foreach ($insights->all() as $insight) {
            if ($insight instanceof SyntaxCheck) {
                self::assertTrue($insight->hasIssue());
                $detail = $insight->getDetails()[0];
                self::assertStringNotContainsString('Fixtures/InvalidPhpCode/_ide_helper.php', $detail->getFile());
                self::assertStringNotContainsString('PHP syntax error: Cannot use League\\Container', $detail->getMessage());
            }
        }
    }
}
