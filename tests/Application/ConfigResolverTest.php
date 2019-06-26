<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Domain\Exceptions\PresetNotFound;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;

final class ConfigResolverTest extends TestCase
{
    /**
     * @var string
     */
    private $baseFixturePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseFixturePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ConfigResolver' . DIRECTORY_SEPARATOR;
    }

    public function testGuessDirectoryWithoutComposer(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath);
        self::assertSame('default', $preset);
    }

    public function testGuessComposerWithoutRequire(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerWithoutRequire');
        self::assertSame('default', $preset);
    }

    public function testGuessSymfony(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerSymfony');
        self::assertSame('symfony', $preset);
    }

    public function testGuessLaravel(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerLaravel');
        self::assertSame('laravel', $preset);
    }

    public function testGuessYii(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerYii');
        self::assertSame('yii', $preset);
    }

    public function testGuessMagento2(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerMagento2');
        self::assertSame('magento2', $preset);
    }

    public function testGuessDrupal(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerDrupal');
        self::assertSame('drupal', $preset);
    }

    public function testGuessWordPress(): void
    {
        $preset = ConfigResolver::guess($this->baseFixturePath . 'ComposerWordPress');
        self::assertSame('wordpress', $preset);
    }

    public function testResolvedConfigIsCorrectlyMerged(): void
    {
        $config = [
            'exclude' => [
                'my/path',
            ],
            'config' => [
                DocCommentSpacingSniff::class => [
                    'linesCountBetweenDifferentAnnotationsTypes' => 2
                ]
            ]
        ];

        $finalConfig = ConfigResolver::resolve($config, $this->baseFixturePath . 'ComposerWithoutRequire');

        self::assertArrayHasKey('exclude', $finalConfig);
        self::assertArrayHasKey('config', $finalConfig);
        self::assertContains('my/path', $finalConfig['exclude']);
        // assert we don't replace the first value
        self::assertContains('bower_components', $finalConfig['exclude']);
        self::assertArrayHasKey(DocCommentSpacingSniff::class, $finalConfig['config']);
        // assert we replace the config value
        self::assertEquals(
            2,
            $finalConfig['config'][DocCommentSpacingSniff::class]['linesCountBetweenDifferentAnnotationsTypes']
        );
    }

    public function testUnknownPresetThrowException(): void
    {
        self::expectException(PresetNotFound::class);
        self::expectExceptionMessage('UnknownPreset not found');

        $config = ['preset' => 'UnknownPreset'];

        ConfigResolver::resolve($config, $this->baseFixturePath . 'ComposerWithoutRequire');
    }

}
