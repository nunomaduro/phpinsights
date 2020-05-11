<?php

declare(strict_types=1);

namespace Tests\Domain\Rector;

use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions;
use Tests\TestCase;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

final class RectorDecoratorTest extends TestCase
{
    public function testItCanDetectPossibleClosureToFunctionChanges(): void
    {
        $fileLocation = __DIR__ . '/../../Feature/Rector/Fixtures/ClosureToArrowFunction.php';

        $collection = $this->runAnalyserOnConfig(
            [
                'config' => [
                    ClosureToArrowFunctionRector::class => [],
                ],
            ],
            [$fileLocation]
        );

        $closureTooArrowErrors = 0;

        /** @var Insight $insight */
        foreach ($collection->allFrom(new Functions()) as $insight) {
            if ($insight->hasIssue() && $insight->getInsightClass() === ClosureToArrowFunctionRector::class) {
                $closureTooArrowErrors++;
            }
        }

        self::assertEquals(1, $closureTooArrowErrors);
    }
}
