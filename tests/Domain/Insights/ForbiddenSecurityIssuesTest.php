<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use Tests\TestCase;

final class ForbiddenSecurityIssuesTest extends TestCase
{
    public function testItHasIssueOnComposerLockWithKnownVulnerability(): void
    {
        $path = __DIR__ . '/Composer/Fixtures/Vulnerable';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ForbiddenSecurityIssues($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertSame(
            'symfony/intl@v3.3.12 CVE-2017-16654: Intl bundle readers breaking out of paths - https://symfony.com/cve-2017-16654',
            $insight->getDetails()[0]->getMessage()
        );
    }

    public function testItHasNoIssueOnProjectComposerLock(): void
    {
        $path = getcwd();
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));

        $insight = new ForbiddenSecurityIssues($collector, []);

        if ($insight->hasIssue()) {
            self::markTestSkipped('Whoops ! Check our local dependencies');
        }

        self::assertFalse($insight->hasIssue());
    }

    public function testItCanCallApiWithVeryLargeLockFile(): void
    {
        // composer.lock taken from https://raw.githubusercontent.com/oroinc/orocommerce-application/4.1.11/composer.lock
        $path = __DIR__ . '/Composer/Fixtures/LargeLockFile';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ForbiddenSecurityIssues($collector, []);

        self::assertTrue($insight->hasIssue());
    }
}
