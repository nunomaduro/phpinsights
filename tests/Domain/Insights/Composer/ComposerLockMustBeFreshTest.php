<?php

declare(strict_types=1);

namespace Tests\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerLockMustBeFresh;
use PHPUnit\Framework\TestCase;

final class ComposerLockMustBeFreshTest extends TestCase
{
    public function testHasIssueWhenComposerLockWasntUpdateFromComposerJson(): void
    {
        $path = __DIR__ . '/Fixtures/Unfresh';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ComposerLockMustBeFresh($collector, []);

        self::assertTrue($insight->hasIssue());
    }

    public function testHasNoIssueWhenComposerLockUpToDate(): void
    {
        $path = __DIR__ . '/Fixtures/Fresh';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ComposerLockMustBeFresh($collector, []);

        self::assertFalse($insight->hasIssue());
    }
}
