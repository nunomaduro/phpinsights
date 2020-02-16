<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenBadClasses;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;
use NunoMaduro\PhpInsights\Domain\Analyser;
use Tests\TestCase;

final class InvalidPhpCodeTest extends TestCase
{
    public function testNotFailingOnSemiColonAfterExtendClass(): void
    {
        error_reporting(E_NOTICE);

        $file = self::prepareFixtureWithSniff(
            UselessAliasSniff::class,
            __DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php'
        );

        try {
            $file->process();
        } catch (\Throwable $ex) {
            self::assertEquals(
                'Undefined index: scope_closer',
                $ex->getMessage());
            return;
        }

        self::fail('Except "Undefined index" exception');
    }

    public function testNotFailingOnSemiColonAfterExtendClass_1(): void
    {
        $files = [
            __DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php',
        ];

        $analyzer  = new Analyser();
        $collector = $analyzer->analyse(__DIR__ . '/Fixtures/', $files);

        $insight = new ForbiddenBadClasses($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertNotEmpty($insight->getDetails());
    }
}
