<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Decorators;

use NunoMaduro\PhpInsights\Domain\Contracts\Fixable;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Helper\Files;
use Rector\Core\Contract\Rector\PhpRectorInterface;
use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\RectorDefinition\RectorDefinition;
use SplFileInfo;

/**
 * Decorates original rector with additional behavior.
 *
 * @internal
 */
final class RectorDecorator implements RectorInterface, InsightContract, HasDetails, Fixable
{
    use FixPerFileCollector;

    private PhpRectorInterface $rector;

    /**
     * @var array<string, \Symfony\Component\Finder\SplFileInfo>
     */
    private array $excludedFiles;

    /**
     * @var array<\NunoMaduro\PhpInsights\Domain\Details>
     */
    private array $errors = [];

    /**
     * @param array<string> $exclude
     */
    public function __construct(PhpRectorInterface $rector, string $dir, array $exclude)
    {
        $this->rector = $rector;
        $this->excludedFiles = [];

        if (count($exclude) > 0) {
            $this->excludedFiles = Files::find($dir, $exclude);
        }
    }

    public function getDetails(): array
    {
        return $this->errors;
    }

    public function hasIssue(): bool
    {
        return count($this->errors) !== 0;
    }

    public function getTitle(): string
    {
        $rectorClass = $this->getInsightClass();

        $path = explode('\\', $rectorClass);
        $name = (string) array_pop($path);

        $name = str_replace('Rector', '', $name);

        return ucfirst(mb_strtolower(trim((string) preg_replace('/(?<! )[A-Z]/', ' $0', $name)))) . " ($rectorClass)";
    }

    public function getInsightClass(): string
    {
        return get_class($this->rector);
    }

    public function getDefinition(): RectorDefinition
    {
        return $this->rector->getDefinition();
    }

    public function addDetails(string $file, string $diff): void
    {
        $this->errors[] = Details::make()
            ->setFile($file)
            ->setDiff($diff)
            ->setMessage($this->rector->getDefinition()->getDescription() . "\n" . $diff);
    }

    public function skipFilesFromExcludedFiles(SplFileInfo $file): bool
    {
        return array_key_exists(
            (string) $file->getRealPath(),
            $this->excludedFiles
        );
    }
}
