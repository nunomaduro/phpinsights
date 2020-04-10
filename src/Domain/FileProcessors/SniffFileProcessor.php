<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\FileFactory;
use NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class SniffFileProcessor implements FileProcessor
{
    /**
     * @var array<array<\NunoMaduro\PhpInsights\Domain\Insights\SniffDecorator>>
     */
    private array $tokenListeners = [];

    private FileFactory $fileFactory;
    private bool $fixEnabled;

    /**
     * FileProcessor constructor.
     */
    public function __construct(FileFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
        $this->fixEnabled = Container::make()->get(Configuration::class)->hasFixEnabled();
    }

    public function support(InsightContract $insight): bool
    {
        return $insight instanceof SniffDecorator;
    }

    public function addChecker(InsightContract $insight): void
    {
        if (! $insight instanceof SniffDecorator) {
            throw new RuntimeException(sprintf(
                'Unable to add %s, not an Sniff instance',
                get_class($insight)
            ));
        }

        foreach ($insight->register() as $token) {
            $this->tokenListeners[$token][] = $insight;
        }
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        $file = $this->fileFactory->createFromFileInfo($splFileInfo);
        $file->processWithTokenListenersAndFileInfo(
            $this->tokenListeners,
            $splFileInfo,
            $this->fixEnabled
        );

        if ($this->fixEnabled && $file->getFixableCount() !== 0) {
            $file->enableFix();
            $file->fixer->fixFile();
            file_put_contents($splFileInfo->getPathname(), $file->fixer->getContents());
            $file->disableFix();
        }
    }
}
