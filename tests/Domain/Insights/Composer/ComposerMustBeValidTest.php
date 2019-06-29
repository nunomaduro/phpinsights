<?php

declare(strict_types=1);

namespace Tests\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid;
use PHPUnit\Framework\TestCase;

final class ComposerMustBeValidTest extends TestCase
{
    public function testComposerIsNotValid(): void
    {
        $collector = new Collector(__DIR__ . '/Fixtures/Fresh');
        $insight = new ComposerMustBeValid($collector, []);

        self::assertTrue($insight->hasIssue());
        self::assertIsArray($insight->getDetails());
        self::assertContains('composer.json: The property name is required', $insight->getDetails());
        self::assertContains('composer.json: The property description is required', $insight->getDetails());
        self::assertContains('composer.json: No license specified, it is recommended to do so. For closed-source software you may use "proprietary" as license.', $insight->getDetails());
    }

    public function testComposerIsValid(): void
    {
        $collector = new Collector(__DIR__ . '/Fixtures/Valid');
        $insight = new ComposerMustBeValid($collector, []);

        self::assertFalse($insight->hasIssue());
    }
}
