<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Application\Composer;
use Tests\TestCase;

final class ComposerTest extends TestCase
{
    public function testPhpVersion74DoesNotMatchLowestVersion71AndUp(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '^7.1',
            ],
        ]);

        self::assertFalse($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4'));
    }

    public function testPhpVersion70IsNotLowerThen71AndUp(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '^7.1',
            ],
        ]);

        self::assertFalse($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.0'));
    }

    public function testPhpVersionMatchesWhenBothIn74(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '7.4',
            ],
        ]);

        self::assertTrue($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4'));
    }

    public function testPhpVersion74Matches74AndUp(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '^7.4',
            ],
        ]);

        self::assertTrue($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4'));
    }

    public function testPhpVersion74Matches74tilde(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '~7.4',
            ],
        ]);

        self::assertTrue($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4'));
    }

    public function testPhpVersion74Matches74star(): void
    {
        $composer = new Composer([
            'require' => [
                'php' => '7.4*',
            ],
        ]);

        self::assertTrue($composer->lowestPhpVersionIsGreaterThenOrEqualTo('7.4'));
    }
}
