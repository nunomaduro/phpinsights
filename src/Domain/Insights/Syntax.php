<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Application\Console\Formatters\PathShortener;
use NunoMaduro\PhpInsights\Domain\Contracts\GlobalInsight;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use PHP_CodeSniffer\Config;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final class Syntax extends Insight implements HasDetails, GlobalInsight
{
    /**
     * @var array<Details>
     */
    private $details = [];

    public function getTitle(): string
    {
        return 'Syntax Check';
    }

    public function hasIssue(): bool
    {
        return count($this->details) > 0;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function process(): void
    {
        if (class_exists(\JakubOnderka\PhpParallelLint\Application::class) &&
            file_exists(getcwd() . '/vendor/bin/parallel-lint')) {
            $this->processParallelLint();

            return;
        }

        $this->processLint();
    }

    private function processLint(): void
    {
        $cmdLine = '';
        $phpPath = (string) Config::getExecutablePath('php');
        $isAnalyseDir = is_dir($this->collector->getCommonPath());

        foreach ($this->collector->getFiles() as $filename) {
            if ($this->shouldSkipFile($filename)) {
                continue;
            }
            if ($isAnalyseDir === true) {
                $filename = $this->collector->getCommonPath() . DIRECTORY_SEPARATOR . $filename;
            }
            $cmdLine .= sprintf(
                '%s -l -d display_errors=1 -d error_prepend_string="" %s;',
                escapeshellcmd($phpPath),
                escapeshellarg($filename)
            );
        }

        $process = Process::fromShellCommandline($cmdLine);
        $process->run();
        $errors = explode(PHP_EOL, $process->getErrorOutput());

        foreach ($errors as $error) {
            if (preg_match('/^.*error:(.*) in (.*) on line ([\d]+)/m', trim($error), $matches) === 1) {
                $filename = $matches[2];
                if ($isAnalyseDir === true) {
                    $filename = str_replace($this->collector->getCommonPath() . DIRECTORY_SEPARATOR, '', $filename);
                } elseif ($filename === $this->collector->getCommonPath()) {
                    $filename = basename($filename);
                }

                $this->details[] = Details::make()
                    ->setMessage('PHP syntax error: ' . trim($matches[1]))
                    ->setFile($filename)
                    ->setLine((int) $matches[3]);
            }
        }
    }

    private function processParallelLint(): void
    {
        $phpPath = (string) Config::getExecutablePath('php');
        $filesToAnalyse = array_map(static function (string $file): string {
            return escapeshellarg($file);
        }, $this->filterFilesWithoutExcluded($this->collector->getFiles()));

        $cmdLine = sprintf(
            '%s %s --no-colors --no-progress --json %s',
            escapeshellcmd($phpPath),
            escapeshellarg(getcwd() . '/vendor/bin/parallel-lint'),
            implode(' ', $filesToAnalyse)
        );

        $process = Process::fromShellCommandline($cmdLine);
        $process->run();
        $output = json_decode($process->getOutput(), true);
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
}
