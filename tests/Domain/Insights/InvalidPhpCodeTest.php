<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;
use Tests\TestCase;

final class InvalidPhpCodeTest extends TestCase
{
    public function testNotFailingOnSemiColonAfterExtendClass(): void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            UselessAliasSniff::class,
            __DIR__ . "/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php"
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }
}
