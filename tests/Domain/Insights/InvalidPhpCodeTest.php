<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use Tests\TestCase;

final class InvalidPhpCodeTest extends TestCase
{
    public function testNotFailingOnSemiColonAfterExtendClass(): void
    {
        $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php']
        );

        $this->expectNotToPerformAssertions();
    }

    public function testNotFailingOnUnclosedComment(): void
    {
        $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/Fixtures/InvalidPhpCode/UnclosedComment.php']
        );

        $this->expectNotToPerformAssertions();
    }
}
