<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\DetailsCarrier;
use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Helper\Files;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Decorates original php-cs-fixers with additional behavior.
 *
 * @internal
 *
 * @see \Tests\Domain\Fixer\FixerDecoratorTest
 */
final class FixerDecorator implements FixerInterface, InsightContract, DetailsCarrier, Fixable
{
    use FixPerFileCollector;

    private FixerInterface $fixer;

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private array $exclude;

    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Details>
     */
    private array $errors = [];

    /**
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

    public function fix(SplFileInfo $file, Tokens $tokens): void
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

    public function supports(SplFileInfo $file): bool
    {
        if ($this->skipFilesFromExcludedFiles($file)) {
            return false;
        }

        return $this->fixer->supports($file);
    }

    /**
     * Checks if the insight detects an issue.
     */
    public function hasIssue(): bool
    {
        return count($this->errors) !== 0;
    }

    /**
     * Gets the title of the insight.
     */
    public function getTitle(): string
    {
        $fixerClass = $this->getInsightClass();
        $path = explode('\\', $fixerClass);
        $name = array_pop($path);
        $name = str_replace('Fixer', '', $name);

        return ucfirst(mb_strtolower(trim((string) preg_replace('/(?<! )[A-Z]/', ' $0', $name))));
    }

    /**
     * Get the class name of Insight used.
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

    public function addDetails(Details $details): void
    {
        $this->errors[] = $details;
    }

    public function addDiff(string $file, string $diff): void
    {
        $diff = trim(substr($diff, 8));

        $this->errors[] = Details::make()->setFile($file)->setDiff($diff)->setMessage($diff);
    }

    private function skipFilesFromExcludedFiles(SplFileInfo $file): bool
    {
        $path = $file->getRealPath();
        if ($path === false) {
            return false;
        }
        return isset($this->exclude[$path]);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        // TODO: Implement getDefinition() method.
    }
}
