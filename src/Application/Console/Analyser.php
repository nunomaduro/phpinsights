<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use function count;

use NunoMaduro\PhpInsights\Application\Console\Helpers\Row;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\PublisherRepository;
use NunoMaduro\PhpInsights\Domain\Insights\Feedback;
use NunoMaduro\PhpInsights\Domain\Insights\FeedbackFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * @param  \Symfony\Component\Console\Style\SymfonyStyle  $style
     * @param  string  $dir
     *
     * @return void
     */
    public function analyse(SymfonyStyle $style, string $dir): void
    {
        $feedback = $this->feedbackFactory->get($metrics = TableStructure::make(), $dir);

        $rows = [];
        foreach ($metrics as $line => $metricClass) {
            $row = new Row($feedback, $metricClass);
            $rows[$line][0] = $row->getFirstCell();

            if (! class_exists($metricClass)) {
                continue;
            }

            $rows[$line][1] = $row->getSecondCell();
        }

        $quality = $feedback->quality();

        $rows[0][1] = sprintf(
            '<fg=default;options=bold>🔎  Code Quality at </><fg=%s;options=bold>%0.2f%%</>',
            $quality === 100.0 ? 'green' : 'red',
            $quality
        );

        TableFactory::make($style, $rows)->render();
    }
}
