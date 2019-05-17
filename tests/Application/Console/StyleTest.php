<?php

declare(strict_types=1);

namespace Tests\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Style;
use Tests\TestCase;

final class StyleTest extends TestCase
{
    public function testStringHasCorrectLengthWhenOneDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Style::class, 'getPercentageAsString', [1]);

        self::assertEquals(5, strlen($percentageString));
    }

    public function testStringHasCorrectLengthWhenTwoDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Style::class, 'getPercentageAsString', [10]);

        self::assertEquals(5, strlen($percentageString));
    }

    public function testStringHasCorrectLengthWhenThreeDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Style::class, 'getPercentageAsString', [100]);

        self::assertEquals(5, strlen($percentageString));
    }
}
