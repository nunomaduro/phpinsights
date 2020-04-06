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
final class SyntaxCheck extends Insight implements HasDetails, GlobalInsight
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
