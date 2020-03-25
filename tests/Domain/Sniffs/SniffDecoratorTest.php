<?php

declare(strict_types=1);

namespace Tests\Domain\Sniffs;

use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneClassPerFileSniff;
use Tests\TestCase;

final class SniffDecoratorTest extends TestCase
{
    public function testCanIgnoreFileInSniffWithFullPath(): void
    {
       $collection = $this->runAnalyserOnConfig(
           [
               'config' => [
                   OneClassPerFileSniff::class => [
                       'exclude' => [
                           __DIR__ . '/../../Fixtures/Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
                       ]
                   ]
               ]
           ],
           [
               __DIR__ . '/../../Fixtures/Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
           ]
       );
       $oneClassPerFileSniffErrors = 0;

       foreach ($collection->allFrom(new Classes) as $insight) {
           if (
               $insight->hasIssue()
               && $insight->getInsightClass() === OneClassPerFileSniff::class
           ) {
               $oneClassPerFileSniffErrors++;
           }
       }

       // No errors of this type as we are ignoring the file.
       self::assertEquals(0, $oneClassPerFileSniffErrors);
    }

    public function testCanIgnoreFileInSniffWithRelativePath(): void
    {
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    OneClassPerFileSniff::class => [
                        'exclude' => [
                            'Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
                        ]
                    ]
                ]
            ],
            [
                __DIR__ . '/../../Fixtures/Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
            ],
            [
                __DIR__ . '/../../Fixtures/'
            ]
        );
        $oneClassPerFileSniffErrors = 0;

        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OneClassPerFileSniff::class
            ) {
                $oneClassPerFileSniffErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $oneClassPerFileSniffErrors);
    }

    public function testFindMoreThanOneClassInFile(): void
    {
        $collection = $this->runAnalyserOnConfig(
            [
            ],
            [
                __DIR__ . '/../../Fixtures/Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
            ]
        );
        $oneClassPerFileSniffErrors = 0;

        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OneClassPerFileSniff::class
            ) {
                $oneClassPerFileSniffErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(1, $oneClassPerFileSniffErrors);
    }

    public function testConfigExcludeDirectory(): void
    {
        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    OneClassPerFileSniff::class => [
                        'exclude' => [
                            'Domain/Sniffs/'
                        ]
                    ]
                ]
            ],
            [
                __DIR__ . '/../../Fixtures/Domain/Sniffs/SniffWrapper/FileWithTwoClasses.php'
            ],
            [
                __DIR__ . '/../../Fixtures/'
            ]
        );
        $oneClassPerFileSniffErrors = 0;

        foreach ($collection->allFrom(new Classes) as $insight) {
            if (
                $insight->hasIssue()
                && $insight->getInsightClass() === OneClassPerFileSniff::class
            ) {
                $oneClassPerFileSniffErrors++;
            }
        }

        // No errors of this type as we are ignoring the file.
        self::assertEquals(0, $oneClassPerFileSniffErrors);
    }
}
