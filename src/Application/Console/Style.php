<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure;
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
     *
     * @return void
     * @throws \ReflectionException
     */
    public function header(Results $results): void
    {
        $subtitle = 'fg=white;options=bold;fg=white';
        $this->newLine();

        $codeQualityColor = "bg={$this->getColor($results->getCodeQuality())}";
        $complexityColor = "bg={$this->getColor($results->getComplexity())}";
        $structureColor = "bg={$this->getColor($results->getStructure())}";
        $dependenciesColor = "bg={$this->getColor($results->getDependencies())}";

        $output = <<<EOD
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$dependenciesColor>         </>
      <fg=black;options=bold;$codeQualityColor>  {$this->getPercentageAsString($results->getCodeQuality())}  </>            <fg=black;options=bold;$dependenciesColor>  {$this->getPercentageAsString($results->getComplexity())}  </>            <fg=black;options=bold;$codeQualityColor>  {$this->getPercentageAsString($results->getStructure())}  </>            <fg=black;options=bold;$dependenciesColor>  {$this->getPercentageAsString($results->getDependencies())}  </>
      <$codeQualityColor>         </>            <$complexityColor>         </>            <$structureColor>         </>            <$dependenciesColor>         </>

        <$subtitle>Code</>              <$subtitle>Complexity</>            <$subtitle>Structure</>           <$subtitle>Dependencies</>
EOD;
        $this->write($output);
        $this->newLine(2);

        $this->writeln("Score scale: <fg=red>◼</> 1-49 <fg=yellow>◼</> 50-89 <fg=green>◼</> 90-100");
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return void
     */
    public function code(InsightCollection $insightCollection, Results $results): void
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
    }


    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return void
     */
    public function complexity(InsightCollection $insightCollection, Results $results): void
    {
        $this->newLine();
        // @todo Add insights to complexity
        $this->writeln(sprintf("[COMPLEXITY] %s with average of <title>%s</> cyclomatic complexity",
            "<fg={$this->getColor($results->getComplexity())};options=bold>{$results->getComplexity()} pts</>",
            (new Complexity\Complexity())->getAvg($insightCollection->getCollector())
        ));
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return void
     */
    public function structure(InsightCollection $insightCollection, Results $results): void
    {
        $this->newLine();

        $this->writeln(sprintf("[STRUCTURE] %s within <title>%s</> files",
            "<fg={$this->getColor($results->getStructure())};options=bold>{$results->getStructure()} pts</>",
            (new Structure\Files())->getValue($insightCollection->getCollector())
        ));

        $this->newLine();

        $lines = [];
        foreach ([Structure\Classes::class, Structure\Interfaces::class, Structure\Globally::class, Structure\Traits::class] as $metric) {
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
    }

    /**
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection  $insightCollection
     * @param  \NunoMaduro\PhpInsights\Domain\Results  $results
     *
     * @return void
     */
    public function dependencies(InsightCollection $insightCollection, Results $results): void
    {
        $this->newLine();

        $this->writeln(sprintf("[DEPENDENCIES] %s on <title>%s</> dependencies",
            "<fg={$this->getColor($results->getDependencies())};options=bold>{$results->getDependencies()} pts</>",
            (new Dependencies\Dependencies())->getValue($insightCollection->getCollector())
        ));

        $this->newLine();

        $lines = [];
        foreach ([Dependencies\Globally::class] as $metric) {
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
        if ($percentage >= 90) {
            return 'green';
        } else if ($percentage >= 50) {
            return 'yellow';
        }

        return 'red';
    }
}