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
    /**
     * Prepares a supplied fixture for a supplied sniffer class with the supplied properties.
     *
     * @param string $sniffClassName
     * @param string $fixtureFile
     * @param array<string, string|array<string>>  $properties
     * @return LocalFile
     */
    public function prepareFixtureWithSniff(string $sniffClassName, string $fixtureFile, array $properties = []): LocalFile
    {
        $sniffs = [self::getFilePathFromClass($sniffClassName)];

        $config = new Config();
        $config->standards = [];

        $ruleName = str_replace('Sniff', '', class_basename($sniffClassName));

        /** @var Ruleset $ruleset */
        $ruleset = (new ReflectionClass(Ruleset::class))
            ->newInstanceWithoutConstructor();
        $ruleset->ruleset = [
            "PhpInsights.Sniffs.{$ruleName}" => [
                'properties' => $properties,
            ]
        ];

        $ruleset->registerSniffs($sniffs, [], []);
        $ruleset->populateTokenListeners();
        return new LocalFile($fixtureFile, $ruleset, $config);
    }

    public static function getFilePathFromClass(string $className) : string
    {
        $reflector = new ReflectionClass($className);

        $filename = $reflector->getFileName();

        return $filename === false ? "" : $filename;
    }


    public function testOneErrorIfOneSetter() : void
    {
        // Prepare the sniff
        $file = $this->prepareFixtureWithSniff(
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
        $file = $this->prepareFixtureWithSniff(
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
        $file = $this->prepareFixtureWithSniff(
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
        $file = $this->prepareFixtureWithSniff(
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
        $file = $this->prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/AnonymousClassWithSetter.php"
        );

        // Run the sniff
        $file->process();

        self::assertEquals(1, $file->getErrorCount());
    }
}
