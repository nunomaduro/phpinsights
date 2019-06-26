<?php

declare(strict_types=1);


namespace Tests\Domain\Sniffs\ForbiddenSetterMethods;

use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use ReflectionClass;
use Tests\TestCase;

final class ForbiddenSetterMethodsTest extends TestCase
{
    public function testOneErrorIfOneSetter() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/ClassWithSetter.php"
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorIfNoSetter() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/ClassWithNoSetter.php"
        );

        // Run the sniff
        $file->process();

        self::assertEquals(0, $file->getErrorCount());
    }

    public function testErrorOnLaravelAttributeSetter() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/ClassWithLaravelAttributeSetter.php",
            [
                'allowedMethodRegex' => [

                ]
            ]
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorOnLaravelAttributeSetterWithRegex() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/ClassWithLaravelAttributeSetter.php",
            [
                'allowedMethodRegex' => [
                    '/^set.*?Attribute$/',
                ],
            ]
        );

        // Run the sniff
        $file->process();

        self::assertEquals(0, $file->getErrorCount());
    }

    public function testOneErrorIfOneSetterOnAnonymousClass() : void
    {
        // Prepare the sniff
        $file = self::prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/AnonymousClassWithSetter.php"
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }
}
