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

        /** @var \NunoMaduro\PhpInsights\Domain\Configuration $configuration */
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
        $totalFiles = \count($files);

        // No files found
        if ($totalFiles === 0) {
            return;
        }

        // Save in cache the current configuration
        $this->cache->set('current_configuration', $this->configuration);

        $sizeChunk = (int) ceil($totalFiles / $this->threads);
        // Create batch for files
        $filesByThread = array_chunk(
            array_map(static fn (SplFileInfo $file) => $file->getRealPath(), $files),
            $sizeChunk,
            false
        );
        // Create progress bar
        $this->output->writeln('');
        $progressBar = $this->createProgressBar($totalFiles + \count($this->globalInsights));
        $progressBar->setMessage('');
        $progressBar->start();

        $this->cache->set('current_configuration', $this->configuration);

        // retrieve current binary, fallback on expected binary in vendors
        $binary = realpath($_SERVER['argv'][0]) ?? getcwd() . '/vendor/bin/phpinsights';
        $runningProcesses = [];
        for ($i = 0; $i < $this->threads; $i++) {
            if (!\array_key_exists($i, $filesByThread)) {
                // Not enough file to inspects to occupate every threads. Bypass
                continue;
            }
            $process = new Process([PHP_BINARY, $binary, InternalProcessorCommand::NAME, ...$filesByThread[$i] ?? '']);
            $process->start();
            $runningProcesses[] = $process;
        }

        while (\count($runningProcesses) > 0) {
            /** @var Process $runningProcess */
            foreach ($runningProcesses as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    $progressBar->advance(\count($filesByThread[$i]));
                    unset($runningProcesses[$i]);
                }
                usleep(1000);
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
        if (! $this->cache->has($cacheKey)) {
            throw new \Exception('Unable to find data for ' . $file->getPathname());
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
            if (! $insight instanceof DetailsCarrier || ! isset($detailsByInsights[$insight->getInsightClass()])) {
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
            if (! $insight instanceof Fixable || ! isset($fixByInsights[$insight->getInsightClass()])) {
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
}
