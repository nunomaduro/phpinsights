<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator;
use NunoMaduro\PhpInsights\Domain\Insights\InsightFactory;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use NunoMaduro\PhpInsights\Domain\Reflection;
use ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Fakes\FakeFileRepository;

final class InsightFactoryTest extends TestCase
{
    private static $usedInsights = [
        BacktickToShellExecFixer::class,
        ForbiddenPublicPropertySniff::class,
        LineLengthSniff::class,
        YodaStyleFixer::class,
    ];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightFactory
     */
    private $insightFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->insightFactory = new InsightFactory(
            new FakeFileRepository([]),
            '.',
            static::$usedInsights
        );
    }

    public function testMakeFromUnknowImplementThrowException(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(sprintf('Insight `%s` is not instantiable.', FakeFileRepository::class));

        $this->insightFactory->makeFrom(FakeFileRepository::class, [], new NullOutput());
    }

    public function testMakeFromSniffReturnInsight(): void
    {
        $sniff = $this->insightFactory->makeFrom(ForbiddenPublicPropertySniff::class, [], new NullOutput());

        self::assertInstanceOf(SniffContract::class, $sniff);
        self::assertInstanceOf(SniffDecorator::class, $sniff);
    }

    public function testMakeFromFixerReturnInsight(): void
    {
        $fixer = $this->insightFactory->makeFrom(BacktickToShellExecFixer::class, [], new NullOutput());

        self::assertInstanceOf(FixerInterface::class, $fixer);
        self::assertInstanceOf(FixerDecorator::class, $fixer);
    }

    public function testConfigureLineLengthSniff(): void
    {
        $config = [
            'config' => [
                LineLengthSniff::class => [
                    'lineLimit' => 50,
                ],
            ],
        ];

        $sniff = $this->insightFactory->makeFrom(LineLengthSniff::class, $config, new NullOutput());
        self::assertInstanceOf(SniffContract::class, $sniff);
        $reflection = new Reflection($sniff);
        $decoratedSniff = $reflection->get('sniff');
        self::assertEquals(50, $decoratedSniff->lineLimit);
    }

    public function testConfigureYodaStyleFixer(): void
    {
        $config = [
            'config' => [
                YodaStyleFixer::class => [
                    'identical' => false,
                    'equal' => true,
                ],
            ],
        ];

        $fixer = $this->insightFactory->makeFrom(YodaStyleFixer::class, $config, new NullOutput());

        self::assertInstanceOf(FixerInterface::class, $fixer);
        $reflection = new Reflection($fixer);
        $decoratedFixer = $reflection->get('fixer');
        $reflection = new Reflection($decoratedFixer);
        $fixerConfiguration = $reflection->get('configuration');
        self::assertTrue($fixerConfiguration['equal']);
        self::assertFalse($fixerConfiguration['identical']);
    }
}
