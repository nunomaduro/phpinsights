<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Application\DefaultPreset;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class ConfigurationTest extends TestCase
{
    public function testPassEmptyArrayReturnDefaultConfiguration(): void
    {
        $configuration = new Configuration([]);

        self::assertEquals([getcwd()], $configuration->getPaths());
        self::assertEquals(DefaultPreset::class, $configuration->getPreset());
        self::assertEquals([], $configuration->getAdd());
        self::assertEquals([], $configuration->getExcludes());
        self::assertEquals([], $configuration->getConfig());
        self::assertEquals([], $configuration->getRemoves());
    }

    public function testWithNullIde(): void
    {
        $config = [
            'ide' => null,
        ];

        $configuration = new Configuration($config);

        self::assertInstanceOf(NullFileLinkFormatter::class, $configuration->getFileLinkFormatter());
    }

    public function testWithEmptyIde(): void
    {
        $config = [
            'ide' => '',
        ];

        $configuration = new Configuration($config);

        self::assertInstanceOf(NullFileLinkFormatter::class, $configuration->getFileLinkFormatter());
    }

    public function testWithSelectedIde(): void
    {
        $config = [
            'ide' => 'sublime',
        ];

        $configuration = new Configuration($config);
        self::assertInstanceOf(FileLinkFormatter::class, $configuration->getFileLinkFormatter());
    }

    public function testWithUnknownIde(): void
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Unknown IDE "notepad++"');

        $config = [
            'ide' => 'notepad++',
        ];

        new Configuration($config);
    }

    public function testResolveDirectoryPath(): void
    {
        $config = ['paths' => 'tests/..'];
        $configuration = new Configuration($config);

        self::assertSame([getcwd()], $configuration->getPaths());
        self::assertStringNotContainsString('..', $configuration->getPaths()[0]);
    }

    public function testInvalidPresetThrowsException()
    {
        $this->expectException(InvalidOptionsException::class);
        new Configuration(['preset' => \stdClass::class]);
    }
}
