<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Kernel;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
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
     * Holds an instance of the Files Repository.
     *
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * Creates a new instance of the Analyse Command.
     *
     * @param \NunoMaduro\PhpInsights\Application\Console\Analyser                  $analyser
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
     */
    public function __construct(
        Analyser $analyser,
        FilesRepository $filesRepository
    ) {
        $this->analyser = $analyser;
        $this->filesRepository = $filesRepository;
    }

    /**
     * Handle the given input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function __invoke(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $style = new Style($input, OutputDecorator::decorate($output));

        $directory = $this->getDirectory($input);

        foreach (Kernel::getRequiredFiles() as $file) {
            if (!file_exists($directory.DIRECTORY_SEPARATOR.$file)) {
                throw new RuntimeException(
                    "The file `$file` must exist. You should run PHP Insights from the root of your project."
                );
            }
        }

        $results = $this->analyser->analyse(
            $style,
            $this->getConfig($input, $directory),
            $directory
        );

        $hasError = false;
        if ($input->getOption('min-quality') > $results->getCodeQuality()) {
            $style->error('The code quality score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-complexity') > $results->getComplexity()) {
            $style->error('The complexity score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-architecture') > $results->getStructure()) {
            $style->error('The architecture score is too low');
            $hasError = true;
        }

        if ($input->getOption('min-style') > $results->getStyle()) {
            $style->error('The style score is too low');
            $hasError = true;
        }

        if (!(bool) $input->getOption(
                'disable-security-check'
            ) && $results->getTotalSecurityIssues() > 0) {
            $hasError = true;
        }

        $style->newLine();
        $style->writeln(
            '✨ See something that needs to be improved? <bold>Create an issue</> or send us a <bold>pull request</>: <title>https://github.com/nunomaduro/phpinsights</title>'
        );

        return (int) $hasError;
    }

    /**
     * Gets the config from the given input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param string                                          $directory
     *
     * @return array<string, array>
     */
    private function getConfig(InputInterface $input, string $directory): array
    {
        /** @var string|null $configPath */
        $configPath = $input->getOption('config-path');

        if ($configPath === null && file_exists(
                getcwd().DIRECTORY_SEPARATOR.'phpinsights.php'
            )) {
            $configPath = getcwd().DIRECTORY_SEPARATOR.'phpinsights.php';
        }

        return ConfigResolver::resolve(
            $configPath !== null && file_exists(
                $configPath
            ) ? require $configPath : [],
            $directory
        );
    }

    /**
     * Gets the directory from the given input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return string
     */
    private function getDirectory(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument(
                'directory'
            ) ?? $this->filesRepository->getDefaultDirectory();

        if ($directory[0] !== DIRECTORY_SEPARATOR && preg_match(
                '~\A[A-Z]:(?![^/\\\\])~i',
                $directory
            ) === 0) {
            $directory = (string)getcwd().DIRECTORY_SEPARATOR.$directory;
        }

        return $directory;
    }
}
