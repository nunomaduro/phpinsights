<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class CodeClimate implements Formatter
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Format the result to the desired format.
     *
     * @param array<int, string> $metrics
     *
     * @throws Exception
     */
    public function format(InsightCollection $insightCollection, array $metrics): void
    {
        $data = $this->issues($insightCollection, $metrics);

        $this->output->write(json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Outputs the issues errors according to the format.
     *
     * @param array<string> $metrics
     *
     * @return array<int, array<string, array<string, array<string, int|null>|string>|string|null>>
     */
    private function issues(InsightCollection $insightCollection, array $metrics): array
    {
        $data = [];

        $climateCategories = [
            'Code' => 'Clarity',
            'Complexity' => 'Complexity',
            'Architecture' => 'Bug Risk',
            'Style' => 'Style',
            'Security' => 'Security'
        ];

        $detailsComparator = new DetailsComparator();

        foreach ($metrics as $metricClass) {
            $category = explode('\\', $metricClass);
            $category = $category[count($category) - 2];

            /** @var Insight $insight */
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {

                if (! $insight->hasIssue()) {
                    continue;
                }

                if (! $insight instanceof HasDetails) {
                    continue;
                }

                $details = $insight->getDetails();

                usort($details, $detailsComparator);

                /** @var Details $detail */
                foreach ($details as $detail) {
                    $data[] = [
                        'checkname' => $insight->getInsightClass(),
                        'description' => $detail->hasMessage() ? $detail->getMessage() : null,
                        'fingerprint' => md5(
                            implode(
                                [
                                    PathShortener::fileName($detail, $insightCollection->getCollector()->getCommonPath()),
                                    $detail->hasLine() ? $detail->getLine() : null,
                                    $detail->hasMessage() ? $detail->getMessage() : null,
                                ]
                            )
                        ),
                        'location' => [
                            'path' => PathShortener::fileName($detail, $insightCollection->getCollector()->getCommonPath()),
                            'lines' => [
                                'begin' => $detail->hasLine() ? $detail->getLine() : null,
                            ],
                        ],
                        'category' => $climateCategories[$category]
                    ];
                }
            }
        }

        return $data;
    }
}
