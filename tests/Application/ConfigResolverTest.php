<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidPresetException;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Preset as LaravelPreset;
use NunoMaduro\PhpInsights\Application\Composer;
use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Tests\Fakes\FakeInput;

final class ConfigResolverTest extends TestCase
{
    private string $baseFixturePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseFixturePath = dirname(__DIR__) . DIRECTORY_SEPARATOR .
            'Fixtures' . DIRECTORY_SEPARATOR . 'ConfigResolver' . DIRECTORY_SEPARATOR;
    }

    public function testGuessDirectoryWithoutComposer(): void
    {
        $preset = ConfigResolver::guess(new Composer([]));

        self::assertSame('default', $preset);
    }

    public function testGuessComposerWithoutRequire(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath("{$this->baseFixturePath}ComposerWithoutRequire" . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('default', $preset);
    }

    public function testGuessSymfony(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath($this->baseFixturePath . 'ComposerSymfony' . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('symfony', $preset);
    }

    public function testGuessLaravel(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath($this->baseFixturePath . 'ComposerLaravel' . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('laravel', $preset);
    }

    public function testGuessYii(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath($this->baseFixturePath . 'ComposerYii' . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('yii', $preset);
    }

    public function testGuessMagento2(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath($this->baseFixturePath . 'ComposerMagento2' . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('magento2', $preset);
    }

    public function testGuessDrupal(): void
    {
        $preset = ConfigResolver::guess(
            Composer::fromPath($this->baseFixturePath . 'ComposerDrupal' . DIRECTORY_SEPARATOR . 'composer.json')
        );

        self::assertSame('drupal', $preset);
    }

    public function testResolvedConfigIsCorrectlyMerged(): void
    {
        $config = [
            'exclude' => [
                'my/path',
            ],
            'config' => [
                DocCommentSpacingSniff::class => [
                    'linesCountBetweenDifferentAnnotationsTypes' => 2,
                ],
            ],
        ];

        $finalConfig = ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerWithoutRequire'])
        );

        self::assertContains('my/path', $finalConfig->getExcludes());
        // assert we don't replace the first value
        self::assertContains('bower_components', $finalConfig->getExcludes());
        self::assertArrayHasKey(DocCommentSpacingSniff::class, $finalConfig->getConfig());
        // assert we replace the config value
        self::assertEquals(
            2,
            $finalConfig->getConfigForInsight(DocCommentSpacingSniff::class)['linesCountBetweenDifferentAnnotationsTypes']
        );
    }

    public function testUnknownPresetThrowException(): void
    {
        $this->expectException(InvalidPresetException::class);

        $config = ['preset' => 'UnknownPreset'];

        ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerWithoutRequire'])
        );
    }

    public function testUnknownMetricAddedThrowException(): void
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Unable to use "say" class as metric in section add.');

        $config = ['add' => ['say' => 'hello']];
        ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerWithoutRequire'])
        );
    }

    public function testKnownMetricAddedWithNonArrayValueThrowException(): void
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Added insights for metric "' . Classes::class . '" should be in an array.');

        $config = ['add' => [Classes::class => 'hello']];
        ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerWithoutRequire'])
        );
    }

    public function testAddUnknownClassThrowException(): void
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Unable to add "hello" insight, class doesn\'t exists.');

        $config = ['add' => [Classes::class => ['hello']]];
        ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerWithoutRequire'])
        );
    }

    /**
     * @dataProvider provideValidIde
     */
    public function testResolveValidIde(string $ide): void
    {
        $config = ['ide' => $ide];

        $config = ConfigResolver::resolve($config, FakeInput::paths([$this->baseFixturePath]));

        self::assertInstanceOf(FileLinkFormatter::class, $config->getFileLinkFormatter());
        self::assertNotInstanceOf(NullFileLinkFormatter::class, $config->getFileLinkFormatter());
    }

    public function testResolveWithoutIde(): void
    {
        $config = [];

        $config = ConfigResolver::resolve($config, FakeInput::paths([$this->baseFixturePath]));

        self::assertInstanceOf(NullFileLinkFormatter::class, $config->getFileLinkFormatter());
    }

    public function testResolveWithIdePattern(): void
    {
        $config = ['ide' => 'myide://file=%f&line=%l'];

        $config = ConfigResolver::resolve($config, FakeInput::paths([$this->baseFixturePath]));

        self::assertInstanceOf(FileLinkFormatter::class, $config->getFileLinkFormatter());
        self::assertNotInstanceOf(NullFileLinkFormatter::class, $config->getFileLinkFormatter());
    }

    public function testMergeInputRequirements(): void
    {
        $input = new ArrayInput(
            [
                '--not-whitelisted' => 1,
                '--min-complexity' => 1,
                '--directory=.',
            ],
            new InputDefinition([
                new InputArgument('paths'),
                new InputOption('min-complexity'),
                new InputOption('disable-security-check'),
                new InputOption('not-whitelisted'),
            ])
        );

        $config = ConfigResolver::resolve([], $input);

        self::assertEquals(1, $config->getMinComplexity());
    }

    public function testOverridePresetByConfig(): void
    {
        $preset = LaravelPreset::get(new Composer([]));
        $removedRulesByPreset = (array) $preset['remove'];

        $config = [
            'preset' => 'laravel',
            'add' => [
                Classes::class => [
                    $removedRulesByPreset[0],
                ],
            ],
        ];

        $finalConfig = ConfigResolver::resolve(
            $config,
            FakeInput::paths([$this->baseFixturePath . 'ComposerLaravel' . DIRECTORY_SEPARATOR . 'composer.json'])
        );

        self::assertNotContains($removedRulesByPreset[0], $finalConfig->getRemoves());
    }

    /**
     * @return array<string, array<string>>
     */
    public function provideValidIde(): array
    {
        return [
            'Sublime Text' => ['sublime'],
            'PhpStorm' => ['phpstorm'],
            'Visual studio Code' => ['vscode'],
            'Textmate' => ['textmate'],
            'Emacs' => ['textmate'],
            'Atom' => ['atom'],
            'Macvim' => ['macvim'],
        ];
    }
}
