<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Application\Console\Commands\InternalProcessorCommand;
use NunoMaduro\PhpInsights\Domain\Contracts\DetailsCarrier;
use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final class Runner
{
    private const EMPTY_BAR_CHARACTER = '░';

    // light shade character \u2591
    private const PROGRESS_CHARACTER = '';

    private const BAR_CHARACTER = '▓';

    private OutputInterface $output;

    private FilesRepository $filesRepository;

    private CacheInterface $cache;

    private string $cacheKey;

    private int $threads;

    /** @var array<\NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight|\NunoMaduro\PhpInsights\Domain\Contracts\Insight> */
    private array $globalInsights = [];

    /** @var array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight> */
    private array $allInsights = [];

    private Configuration $configuration;

    public function __construct(OutputInterface $output, FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
        $this->output = $output;

        $container = Container::make();

        $this->cache = $container->get(CacheInterface::class);

        $this->configuration = $container->get(Configuration::class);
        $this->cacheKey = $this->configuration->getCacheKey();
        $this->threads = $this->configuration->getNumberOfThreads();
    }

    /**
     * @param array<\NunoMaduro\PhpInsights\Domain\Contracts\Insight> $insights
     */
    public function addInsights(array $insights): void
    {
        $this->allInsights = array_merge($insights, $this->allInsights);
        foreach ($insights as $insight) {
            if ($insight instanceof GlobalInsight) {
                $this->globalInsights[] = $insight;
                continue;
            }
        }
    }

    public function run(): void
    {
        // Get the files.
        $files = $this->filesRepository->getFiles();
        $initialCountOfFiles = \count($files);

        // No files found
        if ($initialCountOfFiles === 0) {
            // no file to inspect, no progress bar, early return.
            return;
        }

        if (! $this->configuration->hasFixEnabled()) {
            // retrieve all files already cached
            $filesCached = array_filter(
                $files,
                fn (SplFileInfo $file): bool => $this->cache->has(
                    'insights.' . $this->cacheKey . '.' . md5($file->getContents())
                )
            );

            // process them
            array_walk(
                $filesCached,
                function (SplFileInfo $file): void {
                    $this->processFile($file);
                }
            );

            // and reduce files to launch inspection
            $files = array_diff($files, $filesCached);
        }

        $totalFiles = \count($files);

        // Save in cache the current configuration
        $this->cache->set('current_configuration', $this->configuration);

        $sizeChunk = (int) ceil($totalFiles / $this->threads);
        // Create batch for files
        $filesByThread = [];
        if ($sizeChunk > 0) {
            $filesByThread = array_chunk(
                array_map(static fn (SplFileInfo $file) => $file->getRealPath(), $files),
                $sizeChunk,
                false
            );
        }

        // Create progress bar
        $this->output->writeln('');
        $progressBar = $this->createProgressBar($initialCountOfFiles + \count($this->globalInsights));
        $progressBar->setMessage('');
        $progressBar->start();

        if ($initialCountOfFiles !== $totalFiles) {
            $progressBar->advance($initialCountOfFiles - $totalFiles);
            $progressBar->display();
        }

        $binary = $this->retrieveBinaryPath();
        /** @var array<Process> $runningProcesses */
        $runningProcesses = [];
        for ($i = 0; $i < $this->threads; $i++) {
            if (! \array_key_exists($i, $filesByThread)) {
                // Not enough file to inspects to occupate every threads. Bypass
                continue;
            }
            $cacheKey = sprintf('thread-%s-%s', $i, md5(implode('', $filesByThread[$i])));
            $this->cache->set($cacheKey, $filesByThread[$i]);

            $process = new Process([PHP_BINARY, $binary, InternalProcessorCommand::NAME, $cacheKey]);
            $process->start();
            $runningProcesses[] = $process;
        }

        while ($runningProcesses !== []) {
            foreach ($runningProcesses as $i => $runningProcess) {
                if ($runningProcess->isRunning()) {
                    usleep(1000);
                    continue;
                }

                if (! $runningProcess->isSuccessful()) {
                    throw new ProcessFailedException($runningProcess);
                }

                $progressBar->advance(\count($filesByThread[$i]));
                unset($runningProcesses[$i]);
            }
        }

        /** @var GlobalInsight $insight */
        foreach ($this->globalInsights as $insight) {
            $insight->process();
            $progressBar->advance();
        }

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $this->processFile($file);
        }

        $progressBar->setMessage(PHP_EOL . '<info> ✨ Analysis Completed !</info>');

        $this->cache->delete('current_configuration');
        $progressBar->finish();
    }

    private function processFile(SplFileInfo $file): void
    {
        $cacheKey = 'insights.' . $this->cacheKey . '.' . md5($file->getContents());
        // Do not use cache if fix is enabled to force processors to handle it
        if ($this->configuration->hasFixEnabled() && ! $this->cache->has($cacheKey)) {
            throw new \LogicException('Unable to find data for ' . $file->getPathname());
        }

        $this->loadDetailsCache($cacheKey);

        if ($this->configuration->hasFixEnabled()) {
            $cacheKey = str_replace('insights.', 'fix.', $cacheKey);
            $this->loadFixCache($cacheKey);
        }
    }

    private function loadDetailsCache(string $cacheKey): void
    {
        $detailsByInsights = $this->cache->get($cacheKey);
        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($this->allInsights as $insight) {
            if (! $insight instanceof DetailsCarrier) {
                continue;
            }
            if (! isset($detailsByInsights[$insight->getInsightClass()])) {
                continue;
            }
            array_walk(
                $detailsByInsights[$insight->getInsightClass()],
                static function (Details $details) use ($insight): void {
                    $insight->addDetails($details);
                }
            );
        }
    }

    private function loadFixCache(string $cacheKey): void
    {
        $fixByInsights = $this->cache->get($cacheKey);
        /** @var \NunoMaduro\PhpInsights\Domain\Contracts\Insight $insight */
        foreach ($this->allInsights as $insight) {
            if (! $insight instanceof Fixable) {
                continue;
            }
            if (! isset($fixByInsights[$insight->getInsightClass()])) {
                continue;
            }
            array_walk(
                $fixByInsights[$insight->getInsightClass()],
                static function (Details $details) use ($insight): void {
                    $insight->addFileFixed($details->getFile());
                }
            );
        }

        $this->cache->delete($cacheKey);
    }

    private function createProgressBar(int $max = 0): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $max); // dark shade character \u2593

        $format = ProgressBar::getFormatDefinition($this->getProgressFormat());
        $format .= PHP_EOL . '%message%';

        ProgressBar::setFormatDefinition('phpinsight', $format);
        $progressBar->setFormat('phpinsight');

        if (\DIRECTORY_SEPARATOR !== '\\' || getenv('TERM_PROGRAM') === 'Hyper') {
            $progressBar->setEmptyBarCharacter(self::EMPTY_BAR_CHARACTER);
            $progressBar->setProgressCharacter(self::PROGRESS_CHARACTER);
            $progressBar->setBarCharacter(self::BAR_CHARACTER);
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

    private function retrieveBinaryPath(): string
    {
        $binary = realpath($_SERVER['argv'][0]);
        if ($binary === false ||
            mb_strpos(pathinfo($binary, PATHINFO_FILENAME), 'phpinsights') === false) {
            $binary = getcwd() . '/vendor/bin/phpinsights';
        }

        return $binary;
    }
}
