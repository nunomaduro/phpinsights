<?php

declare(strict_types=1);

namespace Tests\Domain\Sniffs\ForbiddenSetterMethods;

use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use Tests\TestCase;

final class ForbiddenSetterMethodsTest extends TestCase
{
    public function testOneErrorIfOneSetter() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . '/Fixtures/ClassWithSetter.php'
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorIfNoSetter() : void
    {
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . '/Fixtures/ClassWithNoSetter.php'
        );

        $file->process();

        self::assertEquals(0, $file->getErrorCount());
    }

    public function testErrorOnLaravelAttributeSetter() : void
    {
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . '/Fixtures/ClassWithLaravelAttributeSetter.php',
            [
                'allowedMethodRegex' => [],
            ]
        );

        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorOnLaravelAttributeSetterWithRegex() : void
    {
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . '/Fixtures/ClassWithLaravelAttributeSetter.php',
            [
                'allowedMethodRegex' => [
                    '/^set.*?Attribute$/',
                ],
            ]
        );

        $file->process();

        self::assertEquals(0, $file->getErrorCount());
    }

    public function testOneErrorIfOneSetterOnAnonymousClass() : void
    {
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . '/Fixtures/AnonymousClassWithSetter.php'
        );

        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }
}
