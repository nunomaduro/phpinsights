<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use InvalidArgumentException;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Json implements Formatter
{
    /** @var OutputInterface */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Format the result to the desired format.
     *
     * @param InsightCollection $insightCollection
     * @param string            $dir
     * @param array<string>     $metrics
     *
     * @throws Exception
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void {
        $results = $insightCollection->results();

        $data = [
            "summary" => [
                "code" => $results->getCodeQuality(),
                "complexity" => $results->getComplexity(),
                'architecture' => $results->getStructure(),
                'style' => $results->getStyle(),
                'security issues' => $results->getTotalSecurityIssues(),
            ],
        ];
        $data += $this->issues($insightCollection, $metrics);

        $json = json_encode($data);

        if ($json === false) {
            throw new InvalidArgumentException("Failed parsing result to JSON.");
        }

        $this->output->write($json);
    }

    /**
     * Outputs the issues errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param array<string>     $metrics
     *
     * @return array<string, array<int, array<string, int|string>>|null>
     */
    public function issues(
        InsightCollection $insightCollection,
        array $metrics
    ): array {
        $previousCategory = null;

        $data = [];
        $current = null;

        foreach ($metrics as $metricClass) {
            /** @var Insight $insight */
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight->hasIssue()) {
                    continue;
                }

                $category = explode('\\', $metricClass);
                $category = $category[count($category) - 2];

                if ($previousCategory !== $category
                    && $previousCategory !== null) {
                    $data[$previousCategory] = $current;
                    $current = [];
                }

                $previousCategory = $category;

                if (! $insight instanceof HasDetails) {
                    $current[] = [
                        'title' => $insight->getTitle(),
                        'insightClass' => $insight->getInsightClass(),
                    ];
                    continue;
                }

                foreach ($insight->getDetails() as $detail) {
                    $information = explode(':', $detail);

                    if (count($information) !== 3) {
                        $current[] = [
                            'title' => $insight->getTitle(),
                            'insightClass' => $insight->getInsightClass(),
                        ];
                        continue;
                    }

                    $current[] = [
                        'title' => $insight->getTitle(),
                        'insightClass' => $insight->getInsightClass(),
                        'file' => $information[0],
                        'line' => (int) $information[1],
                        'message' => $information[2],
                    ];
                }
            }
        }

        return $data;
    }
}
