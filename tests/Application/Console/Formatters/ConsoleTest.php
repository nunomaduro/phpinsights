<?php

declare(strict_types=1);

namespace Tests\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Formatters\Console;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Style\Style;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
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

    public function testItEscapeDetailMessages(): void
    {
        $insight = new class implements Insight, HasDetails {
            public function hasIssue(): bool
            {
                return true;
            }
            public function getTitle(): string
            {
                return 'testing';
            }
            public function getInsightClass(): string
            {
                return 'testing';
            }
            public function getDetails(): array
            {
                return [
                    (new Details())
                        ->setFile('test')
                        ->setMessage('$io->text("Status: <fg=$statusColor>ERROR</>'),
                ];
            }
        };

        $insightCollection = new InsightCollection(
            new Collector([], '.'),
            [Style::class => [$insight]],
            new Configuration([])
        );

        $output = new BufferedOutput();
        $console = new Console(new ArrayInput([]), $output);

        $console->format($insightCollection, [Style::class]);
        self::assertStringContainsString('$io->text("Status: <fg=$statusColor>ERROR</>', $output->fetch());
    }

    public function testItEscapeDetailDiff(): void
    {
        $insight = new class implements Insight, HasDetails {
            public function hasIssue(): bool
            {
                return true;
            }
            public function getTitle(): string
            {
                return 'testing';
            }
            public function getInsightClass(): string
            {
                return 'testing';
            }
            public function getDetails(): array
            {
                return [
                    (new Details())
                        ->setFile('test')
                        ->setDiff('-    $io->text("Status: <fg=$statusColor>ERROR</>')
                        ->setMessage('-    $io->text("Status: <fg=$statusColor>ERROR</>'),
                ];
            }
        };

        $insightCollection = new InsightCollection(
            new Collector([], '.'),
            [Style::class => [$insight]],
            new Configuration([])
        );

        $output = new BufferedOutput();
        $console = new Console(new ArrayInput([]), $output);

        $console->format($insightCollection, [Style::class]);
        self::assertStringContainsString('$io->text("Status: <fg=$statusColor>ERROR</>', $output->fetch());
    }
}
