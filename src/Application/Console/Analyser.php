<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use function count;

use NunoMaduro\PhpInsights\Application\Console\Helpers\Row;
use NunoMaduro\PhpInsights\Domain\Insights\FeedbackFactory;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Quality;

/**
 * @internal
 */
final class Analyser
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\FeedbackFactory
     */
    private $feedbackFactory;

    /**
     * Analyser constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\FeedbackFactory  $feedbackFactory
     */
    public function __construct(FeedbackFactory $feedbackFactory)
    {
        $this->feedbackFactory = $feedbackFactory;
    }

    /**
     * Analyse the given dirs.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Style  $style
     * @param  array  $config
     * @param  string  $dir
     *
     * @return void
     */
    public function analyse(Style $style, array $config, string $dir): void
    {
        $feedback = $this->feedbackFactory->get($metrics = TableStructure::make(), $config, $dir);

        $rows = [];
        foreach ($metrics as $line => $metricClass) {
            $row = new Row($feedback, $metricClass);
            $rows[$line][0] = $row->getFirstCell();

            if (! class_exists($metricClass)) {
                continue;
            }

            $rows[$line][1] = $row->getSecondCell($dir);
        }

        $quality = $feedback->quality();

        TableFactory::make($style, [
            [$style->letter($this->getLetterType($quality->getLetter()), $quality->getLetter()),
                sprintf(
                    "
<fg=default>Code Quality at </><fg=white;options=bold>%0.2f%%</> with <fg=white;options=bold>%d</> issues
                    ",
                    $quality->getPercentage(), $feedback->issuesCount()
                )],
        ])->render();


        TableFactory::make($style, $rows)->render();
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
