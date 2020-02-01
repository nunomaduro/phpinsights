<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\Formatters\FormatResolver;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Application\Console\Style;
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
     * Creates a new instance of the Analyse Command.
     *
     * @param \NunoMaduro\PhpInsights\Application\Console\Analyser $analyser
     */
    public function __construct(Analyser $analyser)
    {
        $this->analyser = $analyser;
    }

    /**
     * Handle the given input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $consoleOutput = $output;
        if ($consoleOutput instanceof ConsoleOutputInterface
            && $input->getOption('format') !== 'console') {
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
        if ($input->getOption('min-quality') > $results->getCodeQuality()) {
            $consoleStyle->error('The code quality score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-complexity') > $results->getComplexity()) {
            $consoleStyle->error('The complexity score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-architecture') > $results->getStructure()) {
            $consoleStyle->error('The architecture score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-style') > $results->getStyle()) {
            $consoleStyle->error('The style score is too low');
            $hasError = true;
        }

        if (!(bool) $input->getOption('disable-security-check') && $results->getTotalSecurityIssues() > 0) {
            $hasError = true;
        }

        $consoleStyle->newLine();
        $consoleStyle->writeln('✨ See something that needs to be improved? <bold>Create an issue</bold> or send us a <bold>pull request</bold>: <title>https://github.com/nunomaduro/phpinsights</title>');

        return (int) $hasError;
    }
}
