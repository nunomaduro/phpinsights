<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use PHP_CodeSniffer\Config;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final class SyntaxCheck extends Insight implements HasDetails, GlobalInsight
{
    /** @var array<Details> */
    private array $details = [];

    public function getTitle(): string
    {
        return 'Syntax Check';
    }

    public function hasIssue(): bool
    {
        return $this->details !== [];
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function process(): void
    {
        $toAnalyse = $this->getTarget();

        $cmdLine = sprintf(
            '%s --no-colors --no-progress --json %s %s',
            $this->getBinary(),
            implode(' ', $this->getShellExcludeArgs()),
            $toAnalyse
        );
        $process = Process::fromShellCommandline($cmdLine);

        if ($toAnalyse === '.' && getcwd() !== rtrim($this->collector->getCommonPath(), DIRECTORY_SEPARATOR)) {
            $process->setWorkingDirectory($this->collector->getCommonPath());
        }
        $configuration = Container::make()->get(Configuration::class);
        $process->setTimeout($configuration->getTimeout())->run();

        $output = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        $errors = $output['results']['errors'] ?? [];

        foreach ($errors as $error) {
            if (preg_match('/^.*error:(.*) in .* on line [\d]+/m', $error['message'], $matches) === 1) {
                $this->details[] = Details::make()
                    ->setFile($error['file'])
                    ->setLine($error['line'])
                    ->setMessage('PHP syntax error: ' . trim($matches[1]));
            }
        }
    }

    private function getBinary(): string
    {
        $parentPath = $this->composerBinaryFolderFind(dirname(__DIR__, 3));

        if (DIRECTORY_SEPARATOR === '\\') {
            return $parentPath . '\parallel-lint.bat';
        }

        return sprintf(
            '%s %s',
            escapeshellcmd((string) Config::getExecutablePath('php')),
            escapeshellarg($parentPath . '/parallel-lint')
        );
    }

    /**
     * Converts all sources of excluded files into a list of escaped `--exclude` args for parallel-lint.
     * This insight uses paths as-is rather than resolving them, As parallel-lint resolves paths itself.
     *
     * @return array<string>
     */
    private function getShellExcludeArgs(): array
    {
        $configuration = Container::make()->get(Configuration::class);

        $rootExcludes = $configuration->getExcludes();
        /** @var array<string> $localExcludes */
        $localExcludes = $this->config['exclude'] ?? [];

        return array_map(
            static fn (string $file): string => '--exclude ' . escapeshellarg($file),
            array_merge($rootExcludes, $localExcludes, LocalFilesRepository::DEFAULT_EXCLUDE)
        );
    }

    private function getTarget(): string
    {
        $files = $this->collector->getFiles();

        if (count($files) === 1) {
            return \array_pop($files);
        }

        return '.';
    }

    /**
     * Recursively search for composer binary folder path.
     */
    private function composerBinaryFolderFind(string $directory): string
    {
        $composerBinaryFolder = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin';

        if (file_exists($directory . $composerBinaryFolder)) {
            return $directory . $composerBinaryFolder;
        }
        if (dirname($directory) === $directory) {
            throw new \RuntimeException('Unable to find composer binary folder');
        }

        return $this->composerBinaryFolderFind(dirname($directory));
    }
}
