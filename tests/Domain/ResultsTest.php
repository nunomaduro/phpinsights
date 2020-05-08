<?php

declare(strict_types=1);

namespace Tests\Domain;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use NunoMaduro\PhpInsights\Domain\Results;
use PHPUnit\Framework\TestCase;

final class ResultsTest extends TestCase
{
    private string $baseFixturePath;

    private string $commonPath;
    /**
     * @var mixed[][]
     */
    private const CATEGORIES = ['Security' => []];

    public function setUp(): void
    {
        parent::setUp();

        $this->baseFixturePath = \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures';
        $this->commonPath = PathShortener::extractCommonPath([$this->baseFixturePath]);
    }

    public function testGetTotalSecurityIssuesReturnZeroWhenForbiddenInsightNotLoaded(): void
    {
        $collector = new Collector([$this->baseFixturePath], $this->commonPath);

        $results = new Results($collector, ['Security' => []]);

        self::assertEquals(0, $results->getTotalSecurityIssues());
    }

    public function testGetTotalSecurityIssuesOnCompromisedCompose(): void
    {
        $path = $this->baseFixturePath . '/Domain/Results/SecurityIssueComposer';
        $collector = new Collector([$path], PathShortener::extractCommonPath([$path]));

        $categories = [
            'Security' => [new ForbiddenSecurityIssues($collector, [])],
        ];

        $results = new Results($collector, $categories);

        self::assertGreaterThan(0, $results->getTotalSecurityIssues());
    }

    public function testHasInsightClassInCategoryReturnFalse(): void
    {
        $collector = new Collector([$this->baseFixturePath], $this->commonPath);

        $result = new Results($collector, self::CATEGORIES);

        self::assertFalse($result->hasInsightInCategory(
            ForbiddenSecurityIssues::class,
            'Security'
        ));
    }

    public function testHasInsightClassInCategoryReturnTrue(): void
    {
        $collector = new Collector([$this->baseFixturePath], $this->commonPath);

        $categories = [
            'Security' => [new ForbiddenSecurityIssues($collector, [])],
        ];

        $result = new Results($collector, $categories);

        self::assertTrue($result->hasInsightInCategory(
            ForbiddenSecurityIssues::class,
            'Security'
        ));
    }
}
