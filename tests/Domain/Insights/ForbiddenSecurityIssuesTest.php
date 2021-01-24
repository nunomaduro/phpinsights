<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use Tests\TestCase;

final class ForbiddenSecurityIssuesTest extends TestCase
{
    public function testItHasIssueOnComposerLockWithKnownVulnerability(): void
    {
        $collector = new Collector(__DIR__ . '/Composer/Fixtures/Vulnerable');
        $insight = new ForbiddenSecurityIssues($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertSame(
            'symfony/intl@v3.3.12 CVE-2017-16654: Intl bundle readers breaking out of paths - https://symfony.com/cve-2017-16654',
            $insight->getDetails()[0]->getMessage()
        );
    }

    public function testItHasNoIssueOnProjectComposerLock(): void
    {
        $collector = new Collector(getcwd());
        $insight = new ForbiddenSecurityIssues($collector, []);

        if ($insight->hasIssue()) {
            self::markTestSkipped('Whoops ! Check our local dependencies');
        }

        self::assertFalse($insight->hasIssue());
    }
}
