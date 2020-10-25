<?php

declare(strict_types=1);

namespace Tests\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Application\Console\Definitions\AnalyseDefinition;
use NunoMaduro\PhpInsights\Application\Console\Formatters\Checkstyle;
use NunoMaduro\PhpInsights\Application\Console\Formatters\Console;
use NunoMaduro\PhpInsights\Application\Console\Formatters\FormatResolver;
use NunoMaduro\PhpInsights\Application\Console\Formatters\GithubAction;
use NunoMaduro\PhpInsights\Application\Console\Formatters\Json;
use NunoMaduro\PhpInsights\Application\Console\Formatters\Multiple;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class FormatResolverTest extends TestCase
{
    private OutputInterface $output;
    private OutputInterface $consoleOutput;

    protected function setUp(): void
    {
        $this->output = new NullOutput();
        $this->consoleOutput = new NullOutput();
    }

    public function testItCreateAConsoleFormatterByDefault(): void
    {
        $input = new ArrayInput([], AnalyseDefinition::get());

        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);

        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(1, $formatters);
        self::assertInstanceOf(Console::class, $formatters[0]);
    }

    public function testItCreateAConsoleFormatter(): void
    {
        $input = new ArrayInput(['--format' => ['console']], AnalyseDefinition::get());
        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);

        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(1, $formatters);
        self::assertInstanceOf(Console::class, $formatters[0]);
    }


    public function testItCreateAJsonFormatter(): void
    {
        $input = new ArrayInput(['--format' => ['json']], AnalyseDefinition::get());

        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);

        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(1, $formatters);
        self::assertInstanceOf(Json::class, $formatters[0]);
    }

    public function testItCreateACheckstyleFormatter(): void
    {
        $input = new ArrayInput(['--format' => ['checkstyle']], AnalyseDefinition::get());

        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);
        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(1, $formatters);
        self::assertInstanceOf(Checkstyle::class, $formatters[0]);
    }

    public function testItCreateAGithubActionFormatter(): void
    {
        $input = new ArrayInput(['--format' => ['github-action']], AnalyseDefinition::get());

        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);

        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(1, $formatters);
        self::assertInstanceOf(GithubAction::class, $formatters[0]);
    }

    public function testItCreateMultipleFormatters(): void
    {
        $input = new ArrayInput(['--format' => ['console', 'checkstyle', 'github-action']], AnalyseDefinition::get());

        $formatter = FormatResolver::resolve($input, $this->output, $this->consoleOutput);

        self::assertInstanceOf(Multiple::class, $formatter);
        $formatters = $this->getFormattersInMultiple($formatter);
        self::assertCount(3, $formatters);
        self::assertInstanceOf(Console::class, $formatters[0]);
        self::assertInstanceOf(Checkstyle::class, $formatters[1]);
        self::assertInstanceOf(GithubAction::class, $formatters[2]);
    }

    /**
     * @return array<Formatter>
     */
    private function getFormattersInMultiple(Formatter $formatter): array
    {
        assert($formatter instanceof Multiple);
        $refProperty = new \ReflectionProperty($formatter, 'formatters');
        $refProperty->setAccessible(true);

        return $refProperty->getValue($formatter);
    }
}
