<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Helper\Files;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Decorates original php-cs-fixers with additional behavior.
 *
 * @internal
 */
final class FixerDecorator implements FixerInterface, InsightContract, HasDetails
{
    /**
     * @var \PhpCsFixer\Fixer\FixerInterface
     */
    private $fixer;
    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private $exclude;
    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Details>
     */
    private $errors = [];

    /**
     * FixerDecorator constructor.
     *
     * @param \PhpCsFixer\Fixer\FixerInterface $fixer
     * @param string $dir
     * @param array<string> $exclude
     */
    public function __construct(FixerInterface $fixer, string $dir, array $exclude)
    {
        $this->fixer = $fixer;
        $this->exclude = [];
        if (count($exclude) > 0) {
            $this->exclude = Files::find($dir, $exclude);
        }
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $this->fixer->isCandidate($tokens);
    }

    public function isRisky(): bool
    {
        return $this->fixer->isRisky();
    }

    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->skipFilesFromExcludedFiles($file)) {
            return;
        }

        $this->fixer->fix($file, $tokens);
    }

    public function getName(): string
    {
        return $this->fixer->getName();
    }

    public function getPriority(): int
    {
        return $this->fixer->getPriority();
    }

    public function supports(\SplFileInfo $file): bool
    {
        if ($this->skipFilesFromExcludedFiles($file)) {
            return false;
        }

        return $this->fixer->supports($file);
    }

    /**
     * Checks if the insight detects an issue.
     *
     * @return bool
     */
    public function hasIssue(): bool
    {
        return count($this->errors) !== 0;
    }

    /**
     * Gets the title of the insight.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $fixerClass = $this->getInsightClass();
        $path = explode('\\', $fixerClass);
        $name = (string) array_pop($path);
        $name = str_replace('Fixer', '', $name);
        return ucfirst(mb_strtolower(trim((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name))));
    }

    /**
     * Get the class name of Insight used.
     *
     * @return string
     */
    public function getInsightClass(): string
    {
        return get_class($this->fixer);
    }

    /**
     * Returns the details of the insight.
     *
     * @return array<int, \NunoMaduro\PhpInsights\Domain\Details>
     */
    public function getDetails(): array
    {
        return $this->errors;
    }

    public function addDiff(string $file, string $diff): void
    {
        $this->processDiff($diff, $file);
    }

    private function skipFilesFromExcludedFiles(\SplFileInfo $file): bool
    {
        return array_key_exists(
            (string) $file->getRealPath(),
            $this->exclude
        );
    }

    private function processDiff(string $diff, string $file): void
    {
        $parsedDiff = $this->splitStringByLines($diff);
        // Get first line number & Remove headers of diff
        $currentLineNumber = $this->parseLineNumber($parsedDiff[2]);
        $parsedDiff = array_slice($parsedDiff, 3);
        $headerMessage = "You should change the following \n";

        $currentDetail = Details::make();
        $currentDetail->setFile($file);
        $currentDetail->setLine($currentLineNumber);
        $currentMessage = $headerMessage;
        $hasColor = false;

        foreach ($parsedDiff as $diffLine) {
            if (mb_strpos($diffLine, '@@ ') === 0) {
                $currentDetail->setMessage($currentMessage);
                $this->errors[] = clone $currentDetail;
                $currentDetail->setLine($this->parseLineNumber($diffLine));
                $currentMessage = $headerMessage;
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

        $currentDetail->setMessage($currentMessage);
        $this->errors[] = $currentDetail;
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
