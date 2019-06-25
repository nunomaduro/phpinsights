<?php

declare(strict_types=1);

namespace Tests\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Formatters\Console;
use Tests\TestCase;

final class ConsoleTest extends TestCase
{
    public function testStringHasCorrectLengthWhenOneDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Console::class, 'getPercentageAsString', [1]);

        self::assertEquals(5, strlen($percentageString));
    }

    public function testStringHasCorrectLengthWhenTwoDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Console::class, 'getPercentageAsString', [10]);

        self::assertEquals(5, strlen($percentageString));
    }

    public function testStringHasCorrectLengthWhenThreeDigitValue(): void
    {
        $percentageString = self::invokeStaticMethod(Console::class, 'getPercentageAsString', [100]);

        self::assertEquals(5, strlen($percentageString));
    }
}
