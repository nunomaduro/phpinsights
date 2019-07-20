<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\CSFixer;
use NunoMaduro\PhpInsights\Domain\Insights\InsightFactory;
use NunoMaduro\PhpInsights\Domain\Insights\Sniff;
use NunoMaduro\PhpInsights\Domain\Reflection;
use ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeFileRepository;

final class InsightFactoryTest extends TestCase
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightFactory
     */
    private $insightFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->insightFactory = new InsightFactory(new FakeFileRepository([]), '.', []);
    }

    public function testMakeFromUnknowImplementThrowException(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(sprintf('Insight `%s` is not instantiable.', FakeFileRepository::class));

        $this->insightFactory->makeFrom(FakeFileRepository::class, []);
    }

    public function testMakeFromSniffReturnInsight(): void
    {
        $sniff = $this->insightFactory->makeFrom(ForbiddenPublicPropertySniff::class, []);

        self::assertInstanceOf(Sniff::class, $sniff);
    }

    public function testMakeFromFixerReturnInsight(): void
    {
        $fixer = $this->insightFactory->makeFrom(BacktickToShellExecFixer::class, []);

        self::assertInstanceOf(CSFixer::class, $fixer);
    }

    public function testConfigureLineLengthSniff(): void
    {
        $config = [
            'config' => [
                LineLengthSniff::class => [
                    'lineLimit' => 50,
                ]
            ]
        ];

        $sniffs = $this->insightFactory->insightsFrom([LineLengthSniff::class], $config, SniffContract::class);
        foreach ($sniffs as $sniff) {
            self::assertInstanceOf(SniffContract::class, $sniff);
            if ($sniff instanceof LineLengthSniff) {
                self::assertEquals(50, $sniff->lineLimit);
            }
        }
    }

    public function testConfigureYodaStyleFixer(): void
    {
        $config = [
            'config' => [
                YodaStyleFixer::class => [
                    'identical' => false,
                    'equal' => true,
                ]
            ]
        ];

        $fixers = $this->insightFactory->insightsFrom([YodaStyleFixer::class], $config, FixerInterface::class);

        foreach ($fixers as $fixer) {
            self::assertInstanceOf(FixerInterface::class, $fixer);
            if ($fixer instanceof YodaStyleFixer) {
                $reflection = new Reflection($fixer);
                $fixerConfiguration = $reflection->get('configuration');
                self::assertTrue($fixerConfiguration['equal']);
                self::assertFalse($fixerConfiguration['identical']);
            }
        }
    }
}
