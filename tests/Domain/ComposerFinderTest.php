<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use PHPUnit\Framework\TestCase;

final class ComposerFinderTest extends TestCase
{
    public function testGetComposerPath(): void
    {
        $path = __DIR__ . '/Insights/Composer/Fixtures/Valid';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $path = ComposerFinder::getPath($collector);

        self::assertEquals(
            __DIR__ . '/Insights/Composer/Fixtures/Valid/composer.json',
            $path
        );
    }

    public function testGetComposerPathThrowExceptionIfNoComposerJsonExist(): void
    {
        $this->expectException(ComposerNotFound::class);

        $path = __DIR__ . '/Insights/Composer/Fixtures';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        ComposerFinder::getPath($collector);
    }
}
