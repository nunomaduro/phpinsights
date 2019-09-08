<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use SplFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @internal
 */
final class Runner
{
    /** @var \NunoMaduro\PhpInsights\Domain\FileProcessors\SniffFileProcessor */
    private $phpCsFileProcessor;

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
    )
    {
        $this->filesRepository = $filesRepository;
        $this->output = $output;

        $container = Container::make();

        $this->phpCsFileProcessor = $container->get(SniffFileProcessor::class);
    }

    /**
     * @param array<SniffDecorator> $sniffs
     */
    public function addSniffs(array $sniffs): void
    {
        foreach ($sniffs as $sniff) {
            $this->phpCsFileProcessor->addSniff($sniff);
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
        $progressBar = new ProgressBar($this->output, count($files));
        $progressBar->start();

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

        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor $fileProcessor */
        foreach ([
            $this->phpCsFileProcessor,
        ] as $fileProcessor) {
            $fileProcessor->processFile($file);
        }
    }
}
