<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Checkstyle implements Formatter
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
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insightCollection
     * @param array<int, string> $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        array $metrics
    ): void {
        if (! extension_loaded('simplexml')) {
            throw new RuntimeException('To use checkstyle format install simplexml extension.');
        }
        $checkstyle = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><checkstyle/>');
        $detailsComparator = new DetailsComparator();

        foreach ($metrics as $metricClass) {
            /** @var Insight $insight */
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight instanceof HasDetails || ! $insight->hasIssue()) {
                    continue;
                }

                $details = $insight->getDetails();
                usort($details, $detailsComparator);

                /** @var Details $detail */
                foreach ($details as $detail) {
                    $fileName = PathShortener::fileName($detail, $insightCollection->getCollector()->getCommonPath());

                    if (isset($checkstyle->file) && (string) $checkstyle->file->attributes()['name'] === $fileName) {
                        $file = $checkstyle->file;
                    } else {
                        $file = $checkstyle->addChild('file');
                        $file->addAttribute('name', $fileName);
                    }

                    $error = $file->addChild('error');
                    $error->addAttribute('severity', 'error');
                    $error->addAttribute('source', $insight->getTitle());
                    $error->addAttribute('line', $detail->hasLine() ? (string) $detail->getLine() : '');
                    $error->addAttribute('message', $detail->hasMessage() ? $detail->getMessage() : '');
                }
            }
        }

        $this->output->write((string) $checkstyle->asXML());
    }
}
