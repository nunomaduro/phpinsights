<?php

declare(strict_types=1);

namespace Tests\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Style;
use Tests\TestCase;

/**
 * @covers \NunoMaduro\PhpInsights\Application\Console\Style
 */
final class StyleTest extends TestCase
{
    /** @test */
    public function stringHasCorrectLengthWhenOneDigitValue() : void
    {
        $percentageString = $this->invokeStaticMethod(Style::class, 'getPercentageAsString', 1);

        $this->assertEquals(5, strlen($percentageString));
    }

    /** @test */
    public function stringHasCorrectLengthWhenTWODigitValue() : void
    {
        $percentageString = $this->invokeStaticMethod(Style::class, 'getPercentageAsString', 10);

        $this->assertEquals(5, strlen($percentageString));
    }

    /** @test */
    public function stringHasCorrectLengthWhenThreeDigitValue() : void
    {
        $percentageString = $this->invokeStaticMethod(Style::class, 'getPercentageAsString', 100);

        $this->assertEquals(5, strlen($percentageString));
    }
}
