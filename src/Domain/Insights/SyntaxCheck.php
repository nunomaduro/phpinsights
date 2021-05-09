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
        return \count($this->details) > 0;
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
        $process->run();

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
        $parentPath = $this->vendorParentPathFind();

        if (DIRECTORY_SEPARATOR === '\\') {
            return $parentPath . '\vendor\bin\parallel-lint.bat';
        }

        return sprintf(
            '%s %s',
            escapeshellcmd((string) Config::getExecutablePath('php')),
            escapeshellarg($parentPath . '/vendor/bin/parallel-lint')
        );
    }

    /**
     * @return array<string>
     */
    private function getShellExcludeArgs(): array
    {
        $configuration = Container::make()->get(Configuration::class);

        $rootExcludes = $configuration->getExcludes();

        return array_map(
            static fn (string $file): string => '--exclude ' . escapeshellarg($file),
            array_merge($rootExcludes, $this->excludedFiles, LocalFilesRepository::DEFAULT_EXCLUDE)
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

    private function vendorParentPathFind(): string
    {
        $baseProjectPath = \dirname(__DIR__, 3);
        if (file_exists($baseProjectPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
            return $baseProjectPath;
        }
        return \dirname(__DIR__, 6);
    }
}
