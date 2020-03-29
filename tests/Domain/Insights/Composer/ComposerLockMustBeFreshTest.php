<?php

declare(strict_types=1);

namespace Tests\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerLockMustBeFresh;
use PHPUnit\Framework\TestCase;

final class ComposerLockMustBeFreshTest extends TestCase
{
    public function testHasIssueWhenComposerLockWasntUpdateFromComposerJson(): void
    {
        $collector = new Collector(__DIR__ . '/Fixtures/Unfresh', []);
        $insight = new ComposerLockMustBeFresh($collector, []);

        self::assertTrue($insight->hasIssue());
    }

    public function testHasNoIssueWhenComposerLockUpToDate(): void
    {
        $collector = new Collector(__DIR__ . '/Fixtures/Fresh', []);
        $insight = new ComposerLockMustBeFresh($collector, []);

        self::assertFalse($insight->hasIssue());
    }
}
