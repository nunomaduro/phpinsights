<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use Exception;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
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
     * @param InsightCollection $insightCollection
     * @param string $dir
     * @param array<string> $metrics
     *
     * @throws Exception
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void
    {
        $checkstyle = new \SimpleXMLElement('<checkstyle/>');

        foreach ($metrics as $metricClass) {
            /** @var Insight $insight */
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight instanceof HasDetails || ! $insight->hasIssue()) {
                    continue;
                }

                /** @var Details $detail */
                foreach ($insight->getDetails() as $detail) {
                    $fileName = $this->getFileName($detail, $dir);

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

    private function getFileName(Details $detail, string $dir): string
    {
        if ($detail->hasFile()) {
            /** replacement is necessary because relative paths are needed */
            return str_replace($dir . '/', '', $detail->getFile());
        }
        return '';
    }
}
