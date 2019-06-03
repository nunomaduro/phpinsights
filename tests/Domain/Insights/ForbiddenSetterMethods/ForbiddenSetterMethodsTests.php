<?php

declare(strict_types=1);


namespace Tests\Domain\Insights\ForbiddenSetterMethods;

use NunoMaduro\PhpInsights\Domain\Reflection;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use ReflectionClass;
use Tests\TestCase;

final class ForbiddenSetterMethodsTests extends TestCase
{
    public function prepareFixtureWithSniff($sniffClassName, string $fixtureFile, array $properties = []): LocalFile
    {
        $sniffs = [self::getFilePathFromClass($sniffClassName)];

        $config = new Config();

        $ruleName = str_replace('Sniff', '', class_basename($sniffClassName));
        $ruleset = new Ruleset($config);
        $ruleset->ruleset = [
            "PhpInsights.Sniffs.{$ruleName}" => [
                'properties' => $properties,
            ]
        ];

        $ruleset->registerSniffs($sniffs, [], []);
        $ruleset->populateTokenListeners();
        return new LocalFile($fixtureFile, $ruleset, $config);
    }

    public static function getFilePathFromClass(string $className)
    {
        $reflector = new ReflectionClass($className);
        return $reflector->getFileName();
    }


    public function testOneErrorIfOneSetter()
    {
        // Prepare the sniff
        $file = $this->prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__."/Fixtures/ClassWithSetter.php"
        );

        // Run the sniff
        $file->process();

        $this->assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorIfNoSetter()
    {
        // Prepare the sniff
        $file = $this->prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__."/Fixtures/ClassWithNoSetter.php"
        );

        // Run the sniff
        $file->process();

        $this->assertEquals(0, $file->getErrorCount());
    }

    public function testErrorOnLaravelAttributeSetter()
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

        $this->assertEquals(1, $file->getErrorCount());
    }

    public function testNoErrorOnLaravelAttributeSetterWithRegex()
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

        $this->assertEquals(0, $file->getErrorCount());
    }

    public function testOneErrorIfOneSetterOnAnonymousClass()
    {
        // Prepare the sniff
        $file = $this->prepareFixtureWithSniff(
            ForbiddenSetterSniff::class,
            __DIR__ . "/Fixtures/AnonymousClassWithSetter.php"
        );

        // Run the sniff
        $file->process();

        $this->assertEquals(1, $file->getErrorCount());
    }
}
