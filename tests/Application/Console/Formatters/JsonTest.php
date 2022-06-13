<?php

declare(strict_types=1);

namespace Tests\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Formatters\Json;
use NunoMaduro\PhpInsights\Domain\Metrics\Style\Style;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

final class JsonTest extends TestCase
{
    public function testItHasSectionForMetricsGiven(): void
    {
        $collection = $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/JsonTest.php']
        );
        $output = new BufferedOutput();
        $input = new ArrayInput(['--summary' => false], new InputDefinition([new InputOption('summary')]));

        $console = new Json($input, $output);

        $console->format($collection, [Style::class]);

        $result = json_decode($output->fetch(), true);
        self::assertArrayHasKey('summary', $result);
        self::assertArrayHasKey('Style', $result);
    }

    public function testItHasOnlySummary(): void
    {
        $collection = $this->runAnalyserOnPreset(
            'default',
            [__DIR__ . '/JsonTest.php']
        );
        $output = new BufferedOutput();
        $input = new ArrayInput(['--summary' => true], new InputDefinition([new InputOption('summary')]));

        $console = new Json($input, $output);

        $console->format($collection, [Style::class]);

        $result = json_decode($output->fetch(), true);
        self::assertArrayHasKey('summary', $result);
        self::assertArrayNotHasKey('Style', $result);
    }
}
