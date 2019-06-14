<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use NunoMaduro\PhpInsights\Domain\Results;
use PHPUnit\Framework\TestCase;

final class ResultsTest extends TestCase
{
    /**
     * @var string
     */
    private $baseFixturePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseFixturePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures';
    }

    public function testGetTotalSecurityIssuesReturnZeroWhenForbiddenInsightNotLoaded(): void
    {
        $collector = new Collector($this->baseFixturePath);
        $results = new Results($collector, ['Security' => []]);

        self::assertEquals(0, $results->getTotalSecurityIssues());
    }

    public function testGetTotalSecurityIssuesOnCompromisedCompose(): void
    {
        $collector = new Collector($this->baseFixturePath . '/Domain/Results/SecurityIssueComposer');
        $categories = [
            'Security' => [new ForbiddenSecurityIssues($collector, [])],
        ];
        $results = new Results($collector, $categories);

        self::assertGreaterThan(0, $results->getTotalSecurityIssues());
    }
}
