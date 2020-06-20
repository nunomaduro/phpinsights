<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Exceptions\InvalidConfiguration;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\FileLinkFormatter;
use NunoMaduro\PhpInsights\Domain\LinkFormatter\NullFileLinkFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ConfigurationTest extends TestCase
{
    public function testPassEmptyArrayReturnDefaultConfiguration(): void
    {
        $configuration = new Configuration([]);

        self::assertEquals([getcwd()], $configuration->getPaths());
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

    public function testGetNumberOfThreads(): void
    {
        if (!file_exists('/usr/bin/nproc')) {
            self::markTestSkipped('Unable to find nproc to get expected cores');
        }

        $command = Process::fromShellCommandline('nproc');
        $command->run();
        $expected = (int) $command->getOutput();

        $configuration = new Configuration([]);
        self::assertSame($expected, $configuration->getNumberOfThreads());
    }

    public function testDefineThreads(): void
    {
        $expected = random_int(1, mt_getrandmax());
        $configuration = new Configuration(['threads' => $expected]);

        self::assertSame($expected, $configuration->getNumberOfThreads());
    }

    public function testDefineNullForThreads(): void
    {
        $configuration = new Configuration(['threads' => null]);

        self::assertGreaterThanOrEqual(1, $configuration->getNumberOfThreads());
    }

    /**
     * @dataProvider invalidThreadsNumber
     */
    public function testExceptionOnInvalidSetThread($invalid): void
    {
        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "threads" with value ' . $invalid . ' is invalid.');
        new Configuration(['threads' => $invalid]);
    }

    public function invalidThreadsNumber(): array
    {
        return [[
            0,
            -1,
            '0',
            '2'
        ]];
    }
}
