<?php

declare(strict_types=1);

namespace Tests\Domain\Insights\Composer;

use Composer\Composer;
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

    public function testComposerIsValidWithDisabledVersionCheck(): void
    {
        $path = __DIR__ . '/Fixtures/WithVersion';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));
        $insight = new ComposerMustBeValid($collector, ['composerVersionCheck' => 0]);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());

        $messages = [];
        /** @var Details $detail */
        foreach ($insight->getDetails() as $detail) {
            self::assertEquals('composer.json', $detail->getFile());
            $messages[] = $detail->getMessage();
        }

        if ($this->isComposerV2()) {
            self::assertNotContains('The version field is present, it is recommended to leave it out if the package is published on Packagist.', $messages);
        } else {
            self::assertContains('The version field is present, it is recommended to leave it out if the package is published on Packagist.', $messages);
        }
    }

    private function isComposerV2(): bool
    {
        return strpos(Composer::VERSION, '2') === 0;
    }
}
