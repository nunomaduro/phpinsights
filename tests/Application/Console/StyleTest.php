<?php

namespace Tests\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Style;
use Tests\TestCase;

/**
 * @covers \NunoMaduro\PhpInsights\Application\Console\Style
 */
class StyleTest extends TestCase
{
    /** @test */
    public function string_has_correct_length_when_one_digit_value()
    {
        $percentageString = Style::getPercentageAsString(1);

        $this->assertEquals(5, strlen($percentageString));
    }

    /** @test */
    public function string_has_correct_length_when_two_digit_value()
    {
        $percentageString = Style::getPercentageAsString(10);

        $this->assertEquals(5, strlen($percentageString));
    }

    /** @test */
    public function string_has_correct_length_when_three_digit_value()
    {
        $percentageString = Style::getPercentageAsString(100);

        $this->assertEquals(5, strlen($percentageString));
    }
}
