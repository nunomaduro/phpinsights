<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator;
use NunoMaduro\PhpInsights\Domain\Insights\InsightFactory;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use NunoMaduro\PhpInsights\Domain\Reflection;
use PHP_CodeSniffer\Sniffs\Sniff as SniffContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Fakes\FakeFileRepository;
use Tests\Fakes\FakeInput;

final class InsightFactoryTest extends TestCase
{
    /**
     * @var array<string>
     */
    private const USED_INSIGHTS = [
        BacktickToShellExecFixer::class,
        ForbiddenPublicPropertySniff::class,
        LineLengthSniff::class,
        YodaStyleFixer::class,
    ];

    /**
     * @var int[][][]
     */
    private const CONFIG = [
        'config' => [
            LineLengthSniff::class => [
                'lineLimit' => 50,
            ],
        ],
    ];

    private InsightFactory $insightFactory;

    public function setUp(): void
    {
        parent::setUp();

        $config = ConfigResolver::resolve([], FakeInput::paths(['.']));

        $this->insightFactory = new InsightFactory(
            new FakeFileRepository([]),
            static::USED_INSIGHTS,
            $config,
            new Collector($config->getPaths(), $config->getCommonPath())
        );
    }

    public function testMakeFromUnknownImplementThrowException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Insight `%s` is not instantiable.', FakeFileRepository::class));

        $this->insightFactory->makeFrom(FakeFileRepository::class, new NullOutput());
    }

    public function testMakeFromSniffReturnInsight(): void
    {
        $sniff = $this->insightFactory->makeFrom(ForbiddenPublicPropertySniff::class, new NullOutput());

        self::assertInstanceOf(SniffContract::class, $sniff);
        self::assertInstanceOf(SniffDecorator::class, $sniff);
    }

    public function testMakeFromFixerReturnInsight(): void
    {
        $fixer = $this->insightFactory->makeFrom(BacktickToShellExecFixer::class, new NullOutput());

        self::assertInstanceOf(FixerInterface::class, $fixer);
        self::assertInstanceOf(FixerDecorator::class, $fixer);
    }

    public function testConfigureLineLengthSniff(): void
    {
        $configuration = ConfigResolver::resolve(self::CONFIG, FakeInput::paths(['.']));

        $insightFactory = new InsightFactory(
            new FakeFileRepository([]),
            static::USED_INSIGHTS,
            $configuration,
            new Collector($configuration->getPaths(), $configuration->getCommonPath())
        );

        $sniff = $insightFactory->makeFrom(LineLengthSniff::class, new NullOutput());
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

        $configuration = ConfigResolver::resolve($config, FakeInput::paths(['.']));

        $insightFactory = new InsightFactory(
            new FakeFileRepository([]),
            static::USED_INSIGHTS,
            $configuration,
            new Collector($configuration->getPaths(), $configuration->getCommonPath())
        );

        $fixer = $insightFactory->makeFrom(YodaStyleFixer::class, new NullOutput());
        self::assertInstanceOf(FixerInterface::class, $fixer);

        $reflection = new Reflection($fixer);
        $decoratedFixer = $reflection->get('fixer');
        $reflection = new Reflection($decoratedFixer);
        $fixerConfiguration = $reflection->get('configuration');

        self::assertTrue($fixerConfiguration['equal']);
        self::assertFalse($fixerConfiguration['identical']);
    }
}
