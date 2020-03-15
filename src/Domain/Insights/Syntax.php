<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

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
        $cmdLine = '';
        $phpPath = (string) Config::getExecutablePath('php');
        $isAnalyseDir = is_dir($this->collector->getDir());

        foreach ($this->collector->getFiles() as $filename) {
            if ($this->shouldSkipFile($filename)) {
                continue;
            }
            if ($isAnalyseDir === true) {
                $filename = $this->collector->getDir() . DIRECTORY_SEPARATOR . $filename;
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
                    $filename = str_replace($this->collector->getDir() . DIRECTORY_SEPARATOR, '', $filename);
                } elseif ($filename === $this->collector->getDir()) {
                    $filename = basename($filename);
                }

                $this->details[] = Details::make()
                    ->setMessage('PHP syntax error: ' . trim($matches[1]))
                    ->setFile($filename)
                    ->setLine((int) $matches[3]);
            }
        }
    }
}
