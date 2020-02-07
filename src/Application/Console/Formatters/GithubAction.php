<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\DetailsComparator;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class GithubAction implements Formatter
{
    /**
     * @var \NunoMaduro\PhpInsights\Application\Console\Formatters\Console
     */
    private $decorated;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $baseDir;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->decorated = new Console($input, $output);
        $this->output = $output;
        $this->baseDir = Container::make()->get(Configuration::class)->getDirectory();
    }

    /**
     * @param \NunoMaduro\PhpInsights\Domain\Insights\InsightCollection $insightCollection
     * @param string $dir
     * @param array<int, string> $metrics
     */
    public function format(InsightCollection $insightCollection, string $dir, array $metrics): void
    {
        // Call The Console Formatter to get summary and recap,
        // not issues by passing an empty array for metrics.
        $this->decorated->format($insightCollection, $dir, []);
        $detailsComparator = new DetailsComparator();

        $errors = [];

        foreach ($insightCollection->all() as $insight) {
            if (! $insight instanceof HasDetails || ! $insight->hasIssue()) {
                continue;
            }

            $details = $insight->getDetails();
            usort($details, $detailsComparator);

            /** @var Details $detail */
            foreach ($details as $detail) {
                if (! $detail->hasFile()) {
                    continue;
                }

                $file = $this->getRelativePath($detail->getFile());
                if (! array_key_exists($file, $errors)) {
                    $errors[$file] = [];
                }

                $message = $this->formatMessage($detail, $insight);
                // replace line 0 to line 1
                // github action write it at line 1 otherwise
                $line = $detail->hasLine() ? $detail->getLine() : 1;

                if (! array_key_exists($line, $errors[$file])) {
                    $errors[$file][$line] = $message;
                    continue;
                }

                $errors[$file][$line] .= "\n" . $message;
            }
        }

        foreach ($errors as $file => $lines) {
            foreach ($lines as $line => $message) {
                // @see https://help.github.com/en/actions/automating-your-workflow-with-github-actions/development-tools-for-github-actions#set-an-error-message-error
                $this->output->writeln(sprintf(
                    '::error file=%s,line=%s::%s',
                    $this->escapeData($file),
                    $line,
                    $this->escapeData($message)
                ));
            }
        }
    }

    private function getRelativePath(string $file): string
    {
        return str_replace($this->baseDir . DIRECTORY_SEPARATOR, '', $file);
    }

    private function formatMessage(Details $detail, Insight $insight): string
    {
        $message = '* [' . $insight->getTitle() . '] ';

        if ($detail->hasMessage()) {
            $message .= $detail->getMessage();
        }

        return $message;
    }

    private function escapeData(string $data): string
    {
        $templates = [
            "\r" => '%0D',
            "\n" => '%0A',
        ];

        return strtr($data, $templates);
    }
}
