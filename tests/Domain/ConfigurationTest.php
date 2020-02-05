<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testPassEmptyArrayReturnDefaultConfiguration(): void
    {
        $configuration = new Configuration([]);

        self::assertEquals(getcwd(), $configuration->getDirectory());
        self::assertEquals('default', $configuration->getPreset());
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

    public function testWithUnknowIde(): void
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Unknow IDE "notepad++"');

        $config = [
            'ide' => 'notepad++',
        ];

        new Configuration($config);
    }

    public function testResolveDirectoryPath(): void
    {
        $config = ['directory' => 'tests/..'];
        $configuration = new Configuration($config);

        self::assertSame(getcwd(), $configuration->getDirectory());
        self::assertStringNotContainsString('..', $configuration->getDirectory());
    }
}
