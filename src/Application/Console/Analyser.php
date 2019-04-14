<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Application\Console\Helpers\Row;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory;
use NunoMaduro\PhpInsights\Domain\Quality;
use Symfony\Component\Console\Terminal;

/**
 * @internal
 */
final class Analyser
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory
     */
    private $insightCollectionFactory;

    /**
     * Analyser constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollectionFactory  $insightCollectionFactory
     */
    public function __construct(InsightCollectionFactory $insightCollectionFactory)
    {
        $this->insightCollectionFactory = $insightCollectionFactory;
    }

    /**
     * Analyse the given dirs.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Style  $style
     * @param  array  $config
     * @param  string  $dir
     *
     * @return float
     */
    public function analyse(Style $style, array $config, string $dir): float
    {
        $insightCollection = $this->insightCollectionFactory->get($metrics = TableStructure::make(), $config, $dir);

        $width = (new Terminal())->getWidth();

        $rows = [];
        foreach ($metrics as $line => $metricClass) {
            $row = new Row($insightCollection, $metricClass);
            $rows[$line][0] = $row->getFirstCell();

            if (! class_exists($metricClass)) {
                continue;
            }

            $rows[$line][1] = '';
            foreach (explode("\n", $row->getSecondCell($dir)) as $key => $rowParts) {
                if ($key === 0) {
                    $rows[$line][1] .= $rowParts;
                    continue;
                }

                if ($key === 1) {
                    $rows[$line][1] .= "\n";
                }

                $rows[$line][1] .= chunk_split($rowParts, $width - strlen($rows[$line][0]), "\n");
            }
            $rows[$line][1] = $rows[$line][1][0] === "\n" ? substr_replace($rows[$line][1], "", -1) : $rows[$line][1];;
        }


        $quality = $insightCollection->quality();

        $style->newLine();

        TableFactory::make($style, [
            [$style->letter($this->getLetterType($quality->getLetter()), $quality->getLetter()),
                sprintf(
                    "
<fg=default>Code Quality at </><fg=white;options=bold>%0.2f%%</> with <fg=white;options=bold>%d</> issues
                    ",
                    $quality->getPercentage(), $insightCollection->issuesCount()
                )],
        ])->render();


        TableFactory::make($style, $rows)->render();

        return $quality->getPercentage();
    }

    /**
     * Returns the type of message of the letter.
     *
     * @return string
     */
    private function getLetterType(string $letter): string
    {
        if ($letter === Quality::VERY_GOOD) {
            return 'green';
        } else if ($letter === Quality::OK) {
            return 'yellow';
        } else if ($letter === Quality::BAD) {
            return 'red';
        } else {
            return 'black';
        }
    }
}
