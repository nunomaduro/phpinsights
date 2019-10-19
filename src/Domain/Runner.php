<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor as FileProcessorContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use SplFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

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
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository $filesRepository
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
        $progressBar->start();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // Output file name if verbose
            if ($this->output->isVerbose()) {
                $this->output->writeln(" {$file->getRealPath()}");
            }

            $this->processFile($file);
            $progressBar->advance();
        }
    }

    private function processFile(SplFileInfo $file): void
    {
        if (! ($file instanceof SymfonySplFileInfo)) {
            $path = $file->getPath()
                . DIRECTORY_SEPARATOR
                . $file->getFilename();

            $file = new SymfonySplFileInfo(
                $path,
                $file->getPath(),
                $path
            );
        }

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

        if ('\\' !== \DIRECTORY_SEPARATOR
            || 'Hyper' === getenv('TERM_PROGRAM')) {
            $progressBar->setEmptyBarCharacter($emptyBarCharacter);
            $progressBar->setProgressCharacter($progressCharacter);
            $progressBar->setBarCharacter($barCharacter);
        }

        return $progressBar;
    }
}
