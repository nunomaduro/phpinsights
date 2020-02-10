<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor as FileProcessorContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class Runner
{
    /** @var array<FileProcessorContract> */
    private $filesProcessors;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    private $output;

    /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository */
    private $filesRepository;

    /**
     * InsightContainer constructor.
     */
    public function __construct(
        OutputInterface $output,
        FilesRepository $filesRepository
    ) {
        $this->filesRepository = $filesRepository;
        $this->output = $output;

        $container = Container::make();

        $this->filesProcessors = $container->get(FileProcessorContract::FILE_PROCESSOR_TAG);
    }

    /**
     * @param array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight> $insights
     */
    public function addInsights(array $insights): void
    {
        /** @var FileProcessorContract $fileProcessor */
        foreach ($this->filesProcessors as $fileProcessor) {
            foreach ($insights as $insight) {
                if ($fileProcessor->support($insight)) {
                    $fileProcessor->addChecker($insight);
                }
            }
        }
    }

    public function run(): void
    {
        // Get the files.
        $files = $this->filesRepository->getFiles();
        $files = iterator_to_array($files);

        // No files found
        if (count($files) === 0) {
            return;
        }

        // Create progress bar
        $progressBar = $this->createProgressBar(count($files));
        $progressBar->setMessage('');
        $progressBar->start();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // Output file name if verbose
            if ($this->output->isVerbose()) {
                $progressBar->setMessage(' - ' . $file->getRelativePathname());
            }

            $this->processFile($file);
            $progressBar->advance();
        }

        if ($this->output->isVerbose()) {
            $progressBar->setMessage(PHP_EOL . '<info>Analysis Completed !</info>');
        }
        $progressBar->finish();
    }

    private function processFile(SplFileInfo $file): void
    {
        /** @var FileProcessorContract $fileProcessor */
        foreach ($this->filesProcessors as $fileProcessor) {
            $fileProcessor->processFile($file);
        }
    }

    private function createProgressBar(int $max = 0): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $max);

        $emptyBarCharacter = '░'; // light shade character \u2591
        $progressCharacter = '';
        $barCharacter = '▓'; // dark shade character \u2593

        $format = ProgressBar::getFormatDefinition($this->getProgressFormat());
        $format .= PHP_EOL . '%message%';
        ProgressBar::setFormatDefinition('phpinsight', $format);
        $progressBar->setFormat('phpinsight');

        if (\DIRECTORY_SEPARATOR !== '\\'
            || getenv('TERM_PROGRAM') === 'Hyper') {
            $progressBar->setEmptyBarCharacter($emptyBarCharacter);
            $progressBar->setProgressCharacter($progressCharacter);
            $progressBar->setBarCharacter($barCharacter);
        }

        return $progressBar;
    }

    private function getProgressFormat(): string
    {
        $verbosity = $this->output->getVerbosity();
        if ($verbosity === OutputInterface::VERBOSITY_VERBOSE) {
            return 'verbose';
        }
        if ($verbosity === OutputInterface::VERBOSITY_VERY_VERBOSE) {
            return 'very_verbose';
        }
        if ($verbosity === OutputInterface::VERBOSITY_DEBUG) {
            return 'debug';
        }
        return 'normal';
    }
}
