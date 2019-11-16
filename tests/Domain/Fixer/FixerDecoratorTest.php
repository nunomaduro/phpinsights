<?php

declare(strict_types=1);

namespace Tests\Domain\Fixer;

use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use Tests\TestCase;

final class FixerDecoratorTest extends TestCase
{
    /**
     * @var string
     */
    private static $fileToTest = 'Domain/Fixer/UnorderedUse.php';

    public function testCanIgnoreFileInFixerWithFullPath(): void
    {
        $fileLocation = __DIR__ . '/../../Fixtures/' . self::$fileToTest;
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

        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OrderedImportsFixer::class
            ) {
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
                        'exclude' => [self::$fileToTest],
                    ],
                ],
            ],
            [
                __DIR__ . '/../../Fixtures/' . self::$fileToTest,
            ],
            __DIR__ . '/../../Fixtures/'
        );
        $orderedImportErrors = 0;

        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OrderedImportsFixer::class
            ) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $orderedImportErrors);
    }

    public function testHasErrorWithNoConfig(): void
    {
        $collection = $this->runAnalyserOnConfig([], [__DIR__ . '/../../Fixtures/' . self::$fileToTest]);
        $orderedImportErrors = 0;

        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OrderedImportsFixer::class
            ) {
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
                __DIR__ . '/../../Fixtures/' . self::$fileToTest,
            ],
            __DIR__ . '/../../Fixtures/'
        );
        $orderedImportErrors = 0;

        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OrderedImportsFixer::class
            ) {
                $orderedImportErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $orderedImportErrors);
    }
}
