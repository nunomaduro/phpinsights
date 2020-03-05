<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\Formatters\FormatResolver;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class AnalyseCommand
{
    /**
     * Holds an instance of the Analyser.
     *
     * @var \NunoMaduro\PhpInsights\Application\Console\Analyser
     */
    private $analyser;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Configuration
     */
    private $configuration;

    /**
     * Creates a new instance of the Analyse Command.
     *
     * @param \NunoMaduro\PhpInsights\Application\Console\Analyser $analyser
     * @param \NunoMaduro\PhpInsights\Domain\Configuration         $configuration
     */
    public function __construct(Analyser $analyser, Configuration $configuration)
    {
        $this->analyser = $analyser;
        $this->configuration = $configuration;
    }

    /**
     * Handle the given input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $consoleOutput = $output;
        $format = $input->getOption('format');
        if ($consoleOutput instanceof ConsoleOutputInterface
            && is_array($format)
            && ! in_array('console', $format, true)) {
            $consoleOutput = $consoleOutput->getErrorOutput();
            $consoleOutput->setDecorated($output->isDecorated());
        }
        $consoleStyle = new Style($input, $consoleOutput);

        $output = OutputDecorator::decorate($output);

        $formatter = FormatResolver::resolve($input, $output, $consoleOutput);

        $results = $this->analyser->analyse(
            $formatter,
            $consoleOutput
        );

        $hasError = false;
        if ($this->configuration->getMinQuality() > $results->getCodeQuality()) {
            $consoleStyle->error('The code quality score is too low');
            $hasError = true;
        }

        if ($this->configuration->getMinComplexity() > $results->getComplexity()) {
            $consoleStyle->error('The complexity score is too low');
            $hasError = true;
        }

        if ($this->configuration->getMinArchitecture() > $results->getStructure()) {
            $consoleStyle->error('The architecture score is too low');
            $hasError = true;
        }

        if ($this->configuration->getMinStyle() > $results->getStyle()) {
            $consoleStyle->error('The style score is too low');
            $hasError = true;
        }

        if (! $this->configuration->isSecurityCheckDisabled() && $results->getTotalSecurityIssues() > 0) {
            $hasError = true;
        }

        $consoleStyle->newLine();
        $consoleStyle->writeln('✨ See something that needs to be improved? <bold>Create an issue</bold> or send us a <bold>pull request</bold>: <title>https://github.com/nunomaduro/phpinsights</title>');

        return (int) $hasError;
    }
}
