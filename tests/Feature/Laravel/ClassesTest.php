<?php

declare(strict_types=1);

namespace Tests\Feature\Laravel;

use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes;
use Tests\TestCase;

/**
 * This test class is for testing if the metrics for Classes gives the correct results with Larave
 *
 * @see Classes
 */
final class ClassesTest extends TestCase
{
    public function testCanAllowAttributeSetters() : void
    {
        $collection = $this->runAnalyserOnPreset(
            'laravel',
            [
                __DIR__ . '/Fixtures/ModelWithAttributeSetter.php',
            ],
            [
                __DIR__ . '/Fixtures',
            ]
        );

        foreach ($collection->allFrom(new Classes) as $insight) {
            self::assertFalse($insight->hasIssue());
        }
    }
}
