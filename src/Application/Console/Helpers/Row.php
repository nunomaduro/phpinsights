<?php


namespace NunoMaduro\PhpInsights\Application\Console\Helpers;


use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\SubCategory;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;

/**
 * @internal
 */
final class Row
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection
     */
    private $insightCollection;

    /**
     * @var string
     */
    private $metricClass;

    /**
     * @var string|null
     */
    private static $category;

    /**
     * Row constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  string  $metricClass
     */
    public function __construct(InsightCollection $insightCollection, string $metricClass)
    {
        $this->insightCollection = $insightCollection;
        $this->metricClass = $metricClass;
    }

    /**
     * Gets the content of the first cell.
     *
     * @return string
     */
    public function getFirstCell(): string
    {
        if (class_exists($name = $this->metricClass)) {
            /** @var \NunoMaduro\PhpInsights\Domain\Contracts\HasValue $metric */
            $metric = new $name();

            /** @var string $a */
            $name = ucfirst(substr((string) strrchr($name, "\\"), 1));

            $name = trim((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name));

            if ($metric instanceof HasPercentage || $metric instanceof SubCategory) {
                $name = '• ' . trim(str_replace((string) self::$category, '', $name));
            } else {
                self::$category = $name;
                $name = "<bold>$name</bold>";
            }

            $name = str_pad(trim($name), 21, ' ');

            if ($metric instanceof HasPercentage && ($percentage = $metric->getPercentage($this->insightCollection->getCollector())) !== 0.00) {
                $name .= sprintf('%.2f%%', $percentage);
            }
        }

        return $name;
    }

    /**
     * Gets the content of the second cell.
     *
     * @param  string  $dir
     *
     * @return string
     */
    public function getSecondCell(string $dir): string
    {
        $metric = new $this->metricClass();

        $cell = $metric instanceof HasValue ? $metric->getValue($this->insightCollection->getCollector()) : '';
        $cell .= $metric instanceof HasAvg ? sprintf(' <fg=magenta>avg %s</>', $metric->getAvg($this->insightCollection->getCollector())) : '';
        $cell .= $metric instanceof HasMax ? sprintf(' <fg=yellow>max %s</>', $metric->getMax($this->insightCollection->getCollector())) : '';
        foreach ($this->insightCollection->allFrom($metric) as $insight) {
            $cell .= $insight->hasIssue() ? '' : ' <info>✔</info>';
        }

        foreach ($this->insightCollection->allFrom($metric) as $insight) {
            if ($insight->hasIssue()) {
                $cell .= "\n<fg=red>✘ --> </>{$insight->getTitle()}";
                if ($insight instanceof HasDetails) {
                    $cell .= ':';
                    $details = $insight->getDetails();
                    $totalDetails = count($details);
                    $details = array_slice($details, -3, 3, true);

                    foreach ($details as $detail) {
                        $detail = str_replace(realpath($dir) . '/', '', $detail);
                        $cell .= "\n<fg=red>•</> $detail";
                    }

                    if ($totalDetails > 3) {
                        $totalRemainDetails = $totalDetails - 3;

                        $cell .= " <fg=red>+{$totalRemainDetails} issues omitted</>";
                    }
                }
            }
        }

        return trim($cell);
    }
}
