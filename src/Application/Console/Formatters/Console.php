<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes as ArchitectureClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Globally as ArchitectureGlobally;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Interfaces as ArchitectureInterfaces;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits as ArchitectureTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use NunoMaduro\PhpInsights\Domain\Results;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Console implements Formatter
{
    /** @var Style */
    private $style;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->style = new Style($input, $output);
    }

    /**
     * Outputs the summary according to the format.
     *
     * @param Results $results
     * @param string  $dir
     *
     * @return self
     */
    public function summary(Results $results, string $dir): self
    {
        $this->style->newLine(2);

        $this->style->writeln(sprintf('<fg=yellow>[%s]</> `%s`', date('Y-m-d H:i:s'), $dir));

        $subtitle = 'fg=white;options=bold;fg=white';
        $this->style->newLine();

        $codeQualityColor = "bg={$this->getColor($results->getCodeQuality())}";
        $complexityColor = "bg={$this->getColor($results->getComplexity())}";
        $structureColor = "bg={$this->getColor($results->getStructure())}";
        $styleColor = "bg={$this->getColor($results->getStyle())}";

        $codeQuality = self::getPercentageAsString($results->getCodeQuality());
        $complexity = self::getPercentageAsString($results->getComplexity());
        $structure = self::getPercentageAsString($results->getStructure());
        $style = self::getPercentageAsString($results->getStyle());

        $output = <<<EOD
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$styleColor>         </>
      <fg=black;options=bold;$codeQualityColor>  {$codeQuality}  </>            <fg=black;options=bold;$complexityColor>  {$complexity}  </>            <fg=black;options=bold;$structureColor>  {$structure}  </>            <fg=black;options=bold;$styleColor>  {$style}  </>
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$styleColor>         </>

        <$subtitle>Code</>               <$subtitle>Complexity</>          <$subtitle>Architecture</>            <$subtitle>Style</>
EOD;
        $this->style->write($output);
        $this->style->newLine(2);

        $this->style->writeln("Score scale: <fg=red>◼</> 1-49 <fg=yellow>◼</> 50-79 <fg=green>◼</> 80-100");

        return $this;
    }

    /**
     * Outputs the code errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results           $results
     *
     * @return self
     */
    public function code(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();
        $this->style->writeln(sprintf("[CODE] %s within <title>%s</title> lines",
            "<fg={$this->getColor($results->getCodeQuality())};options=bold>{$results->getCodeQuality()} pts</>",
            (new Code())->getValue($insightCollection->getCollector())
        ));
        $this->style->newLine();

        $lines = [];
        foreach ([Comments::class, Classes::class, Functions::class, Globally::class] as $metric) {
            $name = explode('\\', $metric);
            $lines[end($name)] = (new $metric())->getPercentage($insightCollection->getCollector());
        }

        foreach ($lines as $name => $percentage) {
            $percentage = number_format((float) $percentage, 1, '.', '');

            $takenSize = strlen($name . $percentage);

            $this->style->writeln(sprintf('%s %s %s %%',
                $name,
                str_repeat('.', 70 - $takenSize),
                $percentage
            ));
        }

        return $this;
    }

    /**
     * Outputs the complexity errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results           $results
     *
     * @return self
     */
    public function complexity(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();

        $this->style->writeln(sprintf("[COMPLEXITY] %s with average of <title>%s</title> cyclomatic complexity",
            "<fg={$this->getColor($results->getComplexity())};options=bold>{$results->getComplexity()} pts</>",
            (new Complexity())->getAvg($insightCollection->getCollector())
        ));

        return $this;
    }

    /**
     * Outputs the architecture errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param Results           $results
     *
     * @return self
     */
    public function architecture(
        InsightCollection $insightCollection,
        Results $results
    ): self {
        $this->style->newLine();

        $this->style->writeln(sprintf("[ARCHITECTURE] %s within <title>%s</title> files",
            "<fg={$this->getColor($results->getStructure())};options=bold>{$results->getStructure()} pts</>",
            (new Files())->getValue($insightCollection->getCollector())
        ));

        $this->style->newLine();

        $lines = [];
        foreach ([
                     ArchitectureClasses::class,
                     ArchitectureInterfaces::class,
                     ArchitectureGlobally::class,
                     ArchitectureTraits::class,
                 ] as $metric) {
            $name = explode('\\', $metric);
            $lines[end($name)] = (new $metric())->getPercentage($insightCollection->getCollector());
        }

        foreach ($lines as $name => $percentage) {
            $percentage = number_format((float) $percentage, 1, '.', '');

            $takenSize = strlen($name . $percentage);

            $this->style->writeln(sprintf('%s %s %s %%',
                $name,
                str_repeat('.', 70 - $takenSize),
                $percentage
            ));
        }

        return $this;
    }

    /**
     * Outputs the miscellaneous errors according to the format.
     *
     * @param Results $results
     *
     * @return self
     */
    public function miscellaneous(
        Results $results
    ): self {
        $this->style->newLine();

        $message = sprintf(
            '[MISC] %s on coding style',
            "<fg={$this->getColor($results->getStyle())};options=bold>{$results->getStyle()} pts</>");

        if ($results->hasInsightInCategory(ForbiddenSecurityIssues::class, 'Security')) {
            $totalSecurityIssuesColor = $results->getTotalSecurityIssues() === 0 ? 'green' : 'red';
            $message .= sprintf(
                ' and %s encountered',
                "<fg={$totalSecurityIssuesColor};options=bold>{$results->getTotalSecurityIssues()} security issues</>"
            );
        }

        $this->style->writeln($message);

        return $this;
    }

    /**
     * Outputs the issues errors according to the format.
     *
     * @param InsightCollection $insightCollection
     * @param array<string>     $metrics
     * @param string            $dir
     *
     * @return self
     */
    public function issues(
        InsightCollection $insightCollection,
        array $metrics,
        string $dir
    ): self {
        $previousCategory = null;

        foreach ($metrics as $metricClass) {
            foreach ($insightCollection->allFrom(new $metricClass()) as $insight) {
                if (! $insight->hasIssue()) {
                    continue;
                }

                $category = explode('\\', $metricClass);
                $category = $category[count($category) - 2];

                if ($previousCategory !== $category) {
                    $this->style->waitForKey($category);
                }

                $previousCategory = $category;

                $issue = "\n<fg=red>•</> [$category] <bold>{$insight->getTitle()}</bold>";

                if (! $insight instanceof HasDetails && ! $this->style->output->isVerbose()) {
                    $this->style->writeln($issue);
                    continue;
                }
                $issue .= ':';
                if ($this->style->output->isVerbose()) {
                    $issue .= " ({$insight->getInsightClass()})";
                }

                if (! $insight instanceof HasDetails) {
                    $this->style->writeln($issue);
                    continue;
                }

                $details = $insight->getDetails();
                $totalDetails = count($details);

                if (! $this->style->output->isVerbose()) {
                    $details = array_slice($details, -3, 3, true);
                }

                foreach ($details as $detail) {
                    $detail = str_replace(realpath($dir) . '/', '', $detail);
                    $issue .= "\n  $detail";
                }

                if (! $this->style->output->isVerbose() && $totalDetails > 3) {
                    $totalRemainDetails = $totalDetails - 3;

                    $issue .= "\n  <fg=red>+{$totalRemainDetails} issues omitted</>";
                }

                $this->style->writeln($issue);
            }
        }

        return $this;
    }


    /**
     * Returns the percentage as 5 chars string.
     *
     * @param  float  $percentage
     *
     * @return string
     */
    private static function getPercentageAsString(float $percentage): string
    {
        $percentageString = sprintf('%s%%', $percentage === 100.0
            ? '100 '
            : number_format($percentage, 1, '.', ''));

        return str_pad($percentageString, 5);
    }

    /**
     * Returns the color for the given percentage.
     *
     * @param  float  $percentage
     *
     * @return string
     */
    private function getColor(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'green';
        }

        if ($percentage >= 50) {
            return 'yellow';
        }

        return 'red';
    }

    /**
     * Format the result to the desired format.
     *
     * @param InsightCollection $insightCollection
     * @param string            $dir
     * @param array<string>     $metrics
     */
    public function format(
        InsightCollection $insightCollection,
        string $dir,
        array $metrics
    ): void
    {
        $results = $insightCollection->results();

        $this->summary($results, $dir)
            ->code($insightCollection, $results)
            ->complexity($insightCollection, $results)
            ->architecture($insightCollection, $results)
            ->miscellaneous($results);

        $this->issues($insightCollection, $metrics, $dir);
    }
}
