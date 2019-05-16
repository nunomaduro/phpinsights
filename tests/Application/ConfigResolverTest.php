<?php

declare(strict_types=1);

namespace Tests\Application;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use PHPUnit\Framework\TestCase;

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
}
