<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use Symplify\EasyCodingStandard\Error\FileDiff;

/**
 * @internal
 */
final class CSFixer implements InsightContract, HasDetails
{
    /**
     * @var array<string>
     */
    private $details;
    /**
     * @var string
     */
    private $fixer;
    /**
     * Creates a new instance of Sniff Insight
     *
     * @param array<string, \Symplify\EasyCodingStandard\Error\FileDiff> $diffs
     */
    public function __construct(array $diffs)
    {
        $this->details = [];

        /** @var FileDiff $fileDiff */
        foreach ($diffs as $file => $fileDiff) {
            if (null === $this->fixer) {
                $this->fixer = $fileDiff->getAppliedCheckers()[0];
            }
            $this->processDiff($fileDiff->getDiff(), $file);
        }
    }

    public function hasIssue(): bool
    {
        return count($this->details) > 0;
    }

    public function getTitle(): string
    {
        $fixerClass = $this->getInsightClass();
        $path = explode('\\', $fixerClass);
        $name = (string) array_pop($path);
        $name = str_replace('Fixer', '', $name);

        return ucfirst(mb_strtolower(trim((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name))));
    }

    public function getInsightClass(): string
    {
        return $this->fixer;
    }

    /**
     * Returns the details of the insight.
     *
     * @return array<string>
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    private function processDiff(string $diff, string $file): void
    {
        $parsedDiff = $this->splitStringByLines($diff);
        // Get first line number & Remove headers of diff
        $currentLineNumber = $this->parseLineNumber($parsedDiff[2]);
        $parsedDiff = array_slice($parsedDiff, 3);
        $headerMessage = "You should change the following \n";
        $currentMessage = sprintf('%s:%s: %s', $file, $currentLineNumber, $headerMessage);
        $hasColor = false;
        foreach ($parsedDiff as $diffLine) {
            if (mb_strpos($diffLine, '@@ ') === 0) {
                $this->details[] = $currentMessage;
                $currentLineNumber = $this->parseLineNumber($diffLine);
                $currentMessage = sprintf('%s:%s: %s', $file, $currentLineNumber, $headerMessage) ;
                continue;
            }

            if (mb_strpos($diffLine, '-') === 0) {
                $hasColor = true;
                $currentMessage .= '<fg=red>';
            }
            if (mb_strpos($diffLine, '+') === 0) {
                $hasColor = true;
                $currentMessage .= '<fg=green>';
            }
            $currentMessage .= $diffLine;
            if ($hasColor) {
                $hasColor = false;
                $currentMessage .= '</>';
            }
        }

        $this->details[] = $currentMessage;
    }

    /**
     * @param string $input
     *
     * @return array<int, string>
     */
    private function splitStringByLines(string $input): array
    {
        $result = \preg_split('/(.*\R)/', $input, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($result === false) {
            throw new \RuntimeException('Unable to split ' . $input);
        }

        return $result;
    }

    private function parseLineNumber(string $diffLine): int
    {
        $pattern = '@^(?:\@\@ -)?([^,]+)@i';
        $matches = null;
        preg_match($pattern, $diffLine, $matches);

        return (int) $matches[1];
    }
}
