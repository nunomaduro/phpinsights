<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\Formatters\FormatResolver;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Kernel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

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
     * Holds an instance of the Files Repository.
     *
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * Creates a new instance of the Analyse Command.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Analyser  $analyser
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     */
    public function __construct(Analyser $analyser, FilesRepository $filesRepository)
    {
        $this->analyser = $analyser;
        $this->filesRepository = $filesRepository;
    }

    /**
     * Handle the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $consoleOutput = $output;
        if ($consoleOutput instanceof ConsoleOutputInterface) {
            $consoleOutput = $consoleOutput->getErrorOutput();
            $consoleOutput->setDecorated(true);
        }
        $consoleStyle = new Style($input, $consoleOutput);

        $output = OutputDecorator::decorate($output);

        $format = FormatResolver::resolve($input, $output);

        $directory = $this->getDirectory($input);

        $isRootAnalyse = true;
        foreach (Kernel::getRequiredFiles() as $file) {
            if (! file_exists($directory . DIRECTORY_SEPARATOR . $file)) {
                $isRootAnalyse = false;
                break;
            }
        }
        $config = $this->getConfig($input, $directory);

        if (! $isRootAnalyse) {
            $config = $this->excludeGlobalInsights($config);
        }
        $results = $this->analyser->analyse(
            $format,
            $config,
            $directory,
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

        if (! (bool) $input->getOption('disable-security-check') && $results->getTotalSecurityIssues() > 0) {
            $hasError = true;
        }

        $consoleStyle->writeln('✨ See something that needs to be improved? <bold>Create an issue</bold> or send us a <bold>pull request</bold>: <title>https://github.com/nunomaduro/phpinsights</title>');

        return (int) $hasError;
    }

    /**
     * Gets the config from the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  string  $directory
     *
     * @return array<string, array>
     */
    private function getConfig(InputInterface $input, string $directory): array
    {
        /** @var string|null $configPath */
        $configPath = $input->getOption('config-path');

        if ($configPath === null && file_exists(getcwd() . DIRECTORY_SEPARATOR . 'phpinsights.php')) {
            $configPath = getcwd() . DIRECTORY_SEPARATOR . 'phpinsights.php';
        }

        return ConfigResolver::resolve($configPath !== null && file_exists($configPath) ? require $configPath : [], $directory);
    }

    /**
     * Gets the directory from the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     *
     * @return string
     */
    private function getDirectory(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument('directory') ?? $this->filesRepository->getDefaultDirectory();

        if ($directory[0] !== DIRECTORY_SEPARATOR && preg_match('~\A[A-Z]:(?![^/\\\\])~i', $directory) === 0) {
            $directory = (string) getcwd() . DIRECTORY_SEPARATOR . $directory;
        }

        return $directory;
    }

    /**
     * @param array<string, array> $config
     *
     * @return array<string, array>
     */
    private function excludeGlobalInsights(array $config): array
    {
        foreach (Kernel::getGlobalInsights() as $insight) {
            $config['remove'][] = $insight;
        }

        return $config;
    }
}
