<?php

declare(strict_types=1);

namespace Tests\Domain\Fixer;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Metrics\Style\Style;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use Tests\TestCase;

final class FixerDecoratorTest extends TestCase
{
    private const FILE_TO_TEST = 'Domain/Fixer/UnorderedUse.php';

    public function testCanIgnoreFileInFixerWithFullPath(): void
    {
        $fileLocation = __DIR__ . '/../../Fixtures/' . self::FILE_TO_TEST;
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    OrderedImportsFixer::class => [
                        'exclude' => [$fileLocation],
                    ],
                ],
            ],
            [$fileLocation]
        );

        $orderedImportErrors = 0;

        /** @var Insight $insight */
        foreach ($collection->allFrom(new Style) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === OrderedImportsFixer::class) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $orderedImportErrors);
    }

    public function testCanIgnoreFileInFixerWithRelativePath(): void
    {
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    OrderedImportsFixer::class => [
                        'exclude' => [self::FILE_TO_TEST],
                    ],
                ],
            ],
            [
                __DIR__ . '/../../Fixtures/' . self::FILE_TO_TEST,
            ],
            [
                __DIR__ . '/../../Fixtures/',
            ]
        );

        $orderedImportErrors = 0;

        /** @var Insight $insight */
        foreach ($collection->allFrom(new Style) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === OrderedImportsFixer::class) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $orderedImportErrors);
    }

    public function testHasErrorWithNoConfig(): void
    {
        $collection = $this->runAnalyserOnConfig([], [__DIR__ . '/../../Fixtures/' . self::FILE_TO_TEST]);
        $orderedImportErrors = 0;

        /** @var Insight $insight */
        foreach ($collection->allFrom(new Style) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === OrderedImportsFixer::class) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(1, $orderedImportErrors);
    }

    public function testConfigExcludeDirectory(): void
    {
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    OrderedImportsFixer::class => [
                        'exclude' => ['Domain/Fixer'],
                    ],
                ],
            ],
            [
                __DIR__ . '/../../Fixtures/' . self::FILE_TO_TEST,
            ],
            [
                __DIR__ . '/../../Fixtures/',
            ]
        );

        $orderedImportErrors = 0;

        /** @var Insight $insight */
        foreach ($collection->allFrom(new Style) as $insight) {
            if ($insight->hasIssue()
                && $insight->getInsightClass() === OrderedImportsFixer::class
            ) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $orderedImportErrors);
    }

    public function testFixableIssues(): void
    {
        $fileToTest = dirname(__DIR__, 2) . '/Feature/Fix/Fixtures/UnorderedUse.php';
        $fileExpected = dirname(__DIR__, 2) . '/Feature/Fix/Fixtures/UnorderedUseExpected.php';

        $initialFileContent = \file_get_contents($fileToTest);

        $this->runAnalyserOnConfig(
            ['fix' => true],
            [$fileToTest]
        );

        self::assertFileEquals($fileExpected, $fileToTest);

        // Restore file content
        file_put_contents($fileToTest, $initialFileContent);
    }
}
