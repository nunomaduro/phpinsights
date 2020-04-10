<?php

declare(strict_types=1);

namespace Tests\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid;
use PHPUnit\Framework\TestCase;

final class ComposerMustBeValidTest extends TestCase
{
    public function testComposerIsNotValid(): void
    {
        $path = __DIR__ . '/Fixtures/Fresh';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ComposerMustBeValid($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());

        $messages = [];
        /** @var Details $detail */
        foreach ($insight->getDetails() as $detail) {
            self::assertEquals('composer.json', $detail->getFile());
            $messages[] = $detail->getMessage();
        }

        self::assertContains('The property name is required', $messages);
        self::assertContains('The property description is required', $messages);
        self::assertContains('No license specified, it is recommended to do so. For closed-source software you may use "proprietary" as license.', $messages);
    }

    public function testComposerIsValid(): void
    {
        $path = __DIR__ . '/Fixtures/Valid';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ComposerMustBeValid($collector, []);

        self::assertFalse($insight->hasIssue());
    }
}
