<?php

declare(strict_types=1);

namespace Tests\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes;
use Tests\TestCase;

final class ForbiddenGlobalsTest extends TestCase
{
    public function testSkipFile(): void
    {
        $file = __DIR__ . '/Fixtures/FileWithMultipleGlobals.php';

        $collection = $this->runAnalyserOnConfig(
            [
                'add' => [
                    Classes::class => [
                        ForbiddenGlobals::class,
                    ],
                ],
                'config' => [
                    ForbiddenGlobals::class => [
                        'exclude' => [$file],
                    ],
                ],
            ],
            [$file]
        );

        $errors = 0;
        foreach ($collection->allFrom(new Classes()) as $insight) {
            if ($insight->getInsightClass() === ForbiddenGlobals::class && $insight->hasIssue()) {
                $errors++;
            }
        }

        self::assertEquals(0, $errors);
    }
}
