<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use PHPUnit\Framework\TestCase;

final class ComposerFinderTest extends TestCase
{
    public function testGetComposerPath(): void
    {
        $collector = new Collector(__DIR__ . '/Insights/Composer/Fixtures/Valid', []);
        $path = ComposerFinder::getPath($collector);

        self::assertEquals(
            __DIR__ . '/Insights/Composer/Fixtures/Valid/composer.json',
            $path
        );
    }

    public function testGetComposerPathThrowExceptionIfNoComposerJsonExist(): void
    {
        $this->expectException(ComposerNotFound::class);

        $collector = new Collector(__DIR__ . '/Insights/Composer/Fixtures', []);
        ComposerFinder::getPath($collector);
    }
}
