<?php

declare(strict_types=1);

namespace Tests\Domain;

use Composer\Composer;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\ComposerLoader;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use PHPUnit\Framework\TestCase;

final class ComposerLoaderTest extends TestCase
{
    public function testGetInstance(): void
    {
        $collector = new Collector([__DIR__ . '/Insights/Composer/Fixtures/Valid']);
        $composer = ComposerLoader::getInstance($collector);

        self::assertEquals(Composer::class, get_class($composer));
    }

    public function testGetExceptionOnFolderWithoutComposerJson(): void
    {
        $this->expectException(ComposerNotFound::class);

        $collector = new Collector([__DIR__ . '/Insights/Composer/Fixtures']);
        ComposerLoader::getInstance($collector);
    }
}
