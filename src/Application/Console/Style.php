<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture;
use NunoMaduro\PhpInsights\Domain\Metrics\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Results;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class Style extends SymfonyStyle
{
    /**
     * Creates a new instance of Style.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     * @param  string  $dir
     *
     * @return $this
     */
    public function header(Results $results, string $dir): Style
    {
        $this->newLine(2);

        $this->writeln(sprintf('<fg=yellow>[%s]</> `%s`', date('Y-m-d H:i:s'), $dir));

        $subtitle = 'fg=white;options=bold;fg=white';
        $this->newLine();

        $codeQualityColor = "bg={$this->getColor($results->getCodeQuality())}";
        $complexityColor = "bg={$this->getColor($results->getComplexity())}";
        $structureColor = "bg={$this->getColor($results->getStructure())}";
        $styleColor = "bg={$this->getColor($results->getStyle())}";

        $output = <<<EOD
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$styleColor>         </>
      <fg=black;options=bold;$codeQualityColor>  {$this->getPercentageAsString($results->getCodeQuality())}  </>            <fg=black;options=bold;$complexityColor>  {$this->getPercentageAsString($results->getComplexity())}  </>            <fg=black;options=bold;$structureColor>  {$this->getPercentageAsString($results->getStructure())}  </>            <fg=black;options=bold;$styleColor>  {$this->getPercentageAsString($results->getStyle())}  </>
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$styleColor>         </>

        <$subtitle>Code</>               <$subtitle>Complexity</>          <$subtitle>Architecture</>            <$subtitle>Style</>          
EOD;
        $this->write($output);
        $this->newLine(2);

        $this->writeln("Score scale: <fg=red>◼</> 1-49 <fg=yellow>◼</> 50-79 <fg=green>◼</> 80-100");

        return $this;
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return $this
     */
    public function code(InsightCollection $insightCollection, Results $results): Style
    {
        $this->newLine();
        $this->writeln(sprintf("[CODE] %s within <title>%s</> lines",
            "<fg={$this->getColor($results->getCodeQuality())};options=bold>{$results->getCodeQuality()} pts</>",
            (new Code\Code())->getValue($insightCollection->getCollector())
        ));
        $this->newLine();

        $lines = [];
        foreach ([Code\Comments::class, Code\Classes::class, Code\Functions::class, Code\Globally::class] as $metric) {
            $name = explode('\\', $metric);
            $lines[end($name)] = (new $metric())
                ->getPercentage($insightCollection->getCollector());
        }

        foreach ($lines as $name => $percentage) {
            $percentage = number_format((float) $percentage, 1, '.', '');

            $takenSize = strlen($name . $percentage);

            $this->writeln(sprintf('%s %s %s %%',
                $name,
                str_repeat('.', 70 - $takenSize),
                $percentage
            ));
        }

        return $this;
    }


    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return $this
     */
    public function complexity(InsightCollection $insightCollection, Results $results): Style
    {
        $this->newLine();

        $this->writeln(sprintf("[COMPLEXITY] %s with average of <title>%s</> cyclomatic complexity",
            "<fg={$this->getColor($results->getComplexity())};options=bold>{$results->getComplexity()} pts</>",
            (new Complexity\Complexity())->getAvg($insightCollection->getCollector())
        ));

        return $this;
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return $this
     */
    public function architecture(InsightCollection $insightCollection, Results $results): Style
    {
        $this->newLine();

        $this->writeln(sprintf("[ARQUICTURE] %s within <title>%s</> files",
            "<fg={$this->getColor($results->getStructure())};options=bold>{$results->getStructure()} pts</>",
            (new Architecture\Files())->getValue($insightCollection->getCollector())
        ));

        $this->newLine();

        $lines = [];
        foreach ([Architecture\Classes::class, Architecture\Interfaces::class, Architecture\Globally::class, Architecture\Traits::class] as $metric) {
            $name = explode('\\', $metric);
            $lines[end($name)] = (new $metric())
                ->getPercentage($insightCollection->getCollector());
        }

        foreach ($lines as $name => $percentage) {
            $percentage = number_format((float) $percentage, 1, '.', '');

            $takenSize = strlen($name . $percentage);

            $this->writeln(sprintf('%s %s %s %%',
                $name,
                str_repeat('.', 70 - $takenSize),
                $percentage
            ));
        }

        return $this;
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return $this
     */
    public function style(Results $results): Style
    {
        $this->newLine();

        $this->writeln(sprintf("[STYLE] %s",
            "<fg={$this->getColor($results->getStyle())};options=bold>{$results->getStyle()} pts</>",
        ));

        return $this;
    }

    /**
     * Describes the issues from the given metrics.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  string[]  $metrics
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Application\Console\Style
     */
    public function issues(InsightCollection $insightCollection, array $metrics, string $dir): Style
    {
        foreach ($metrics as $metricClass) {
            foreach ($insightCollection->allFrom(new $metricClass) as $insight) {
                if (! $insight->hasIssue()) {
                    continue;
                }

                $category = explode('\\', $metricClass);
                $category = $category[count($category) - 2];

                $issue = "\n<fg=red>•</> [$category] <bold>{$insight->getTitle()}</bold>";
                if (! $insight instanceof HasDetails) {
                    $this->writeln($issue);
                    continue;
                }
                $issue .= ':';
                $details = $insight->getDetails();
                $totalDetails = count($details);
                $details = array_slice($details, -3, 3, true);

                foreach ($details as $detail) {
                    $detail = str_replace(realpath($dir) . '/', '', $detail);
                    $issue .= "\n<fg=red>-- </> $detail";
                }

                if ($totalDetails > 3) {
                    $totalRemainDetails = $totalDetails - 3;

                    $issue .= " <fg=red>+{$totalRemainDetails} issues omitted</>";
                }

                $this->writeln($issue);
            }
        }

        return $this;
    }

    /**
     * Returns the percentage as 4 chars string.
     *
     * @param  float  $percentage
     *
     * @return string
     */
    private function getPercentageAsString(float $percentage): string
    {
        return sprintf('%s%%', $percentage === 100.0
            ? '100 '
            : number_format($percentage, 1, '.', ''));
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
}