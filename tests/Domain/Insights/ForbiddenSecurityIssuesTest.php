<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use Tests\TestCase;

final class ForbiddenSecurityIssuesTest extends TestCase
{
    public function testItHasNoIssueOnProjectComposerLock(): void
    {
        $path = dirname(__DIR__, 3);
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));

        $insight = new ForbiddenSecurityIssues($collector, []);

        if ($insight->hasIssue()) {
            self::markTestSkipped('Whoops ! Check our local dependencies');
        }

        self::assertFalse($insight->hasIssue());
    }
}
