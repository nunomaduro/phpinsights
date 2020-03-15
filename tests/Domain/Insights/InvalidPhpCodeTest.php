<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use Tests\TestCase;

final class InvalidPhpCodeTest extends TestCase
{
    /**
     * @return array<string, array<string>>
     */
    public static function invalidCodeProvider(): array
    {
        return [
            'Semi colon after extend class' => [
                __DIR__ . '/Fixtures/InvalidPhpCode/SemiColonAfterExtendClass.php',
            ],
            'Unclosed comment' => [
                __DIR__ . '/Fixtures/InvalidPhpCode/UnclosedComment.php',
            ]
        ];
    }

    /**
     * @dataProvider invalidCodeProvider
     */
    public function testNotFailingOnSemiColonAfterExtendClass(string $path): void
    {
        $this->runAnalyserOnPreset(
            'default',
            [$path]
        );

        $this->expectNotToPerformAssertions();
    }
}
