<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use LogicException;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

/**
 * @internal
 */
final class FixerFileProcessor implements FileProcessor
{
    /**
     * @var array<FixerDecorator>
     */
    private array $fixers = [];

    private DifferInterface $differ;

    private bool $fixEnabled;

    private CacheInterface $cache;

    private string $cacheKey;

    public function __construct(DifferInterface $differ)
    {
        $this->differ = $differ;
        /** @var Configuration $config */
        $config = Container::make()->get(Configuration::class);

        $this->fixEnabled = $config->hasFixEnabled();
        $this->cache = Container::make()->get(CacheInterface::class);
        $this->cacheKey = $config->getCacheKey();
    }

    public function support(InsightContract $insight): bool
    {
        return $insight instanceof FixerDecorator;
    }

    public function addChecker(InsightContract $insight): void
    {
        if (! $insight instanceof FixerDecorator) {
            throw new RuntimeException(sprintf(
                'Unable to add %s, not a Fixer instance',
                get_class($insight)
            ));
        }

        $this->fixers[] = $insight;
    }

    public function processFile(SplFileInfo $splFileInfo): void
    {
        $filePath = $splFileInfo->getRealPath();
        if ($filePath === false) {
            throw new LogicException('Unable to found file ' . $splFileInfo->getFilename());
        }

        $oldContent = $splFileInfo->getContents();
        $cacheKey = $this->cacheKey . '.' . md5($oldContent) . '.fixer';

        if (! $this->fixEnabled && $this->cache->has($cacheKey)) {
            $detailsByFixers = $this->cache->get($cacheKey);
            foreach ($this->fixers as $fixer) {
                if (! isset($detailsByFixers[$fixer->getInsightClass()])) {
                    continue;
                }

                array_walk(
                    $detailsByFixers[$fixer->getInsightClass()],
                    static function (Details $details) use ($fixer): void {
                        $fixer->addDetails($details);
                    }
                );
            }

            return;
        }

        $needFix = false;

        try {
            $tokens = @Tokens::fromCode($oldContent);
            $originalTokens = clone $tokens;
            /** @var FixerDecorator $fixer */
            foreach ($this->fixers as $fixer) {
                $fixer->fix($splFileInfo, $tokens);

                if (! $tokens->isChanged()) {
                    continue;
                }

                if ($this->fixEnabled) {
                    $needFix = true;
                    // Register diff will be applied
                    $fixer->addFileFixed($splFileInfo->getRelativePathname());
                    // Tokens has changed, so we need to clear cache
                    @Tokens::clearCache();
                    $tokens = @Tokens::fromCode($oldContent);

                    continue;
                }

                $fixer->addDiff($filePath, $this->differ->diff($oldContent, $tokens->generateCode()));
                // Tokens has changed, so we need to clear cache
                Tokens::clearCache();
                $tokens = clone $originalTokens;
            }

            if (! $this->fixEnabled || ! $needFix) {
                $this->cacheDetailsForFile($cacheKey, $splFileInfo);
                return;
            }

            $tokens = @Tokens::fromCode($oldContent);
            // Iterate on fixer to get full tokens to change
            foreach ($this->fixers as $fixer) {
                $fixer->fix($splFileInfo, $tokens);
            }

            file_put_contents($splFileInfo->getPathname(), $tokens->generateCode());
        } catch (Throwable $e) {
        }
    }

    private function cacheDetailsForFile(string $cacheKey, SplFileInfo $file): void
    {
        $detailsByFixers = [];
        foreach ($this->fixers as $fixer) {
            if (! $fixer->hasIssue()) {
                continue;
            }
            $details = array_filter(
                $fixer->getDetails(),
                fn (Details $detail): bool => $detail->getFile() === $file->getRealPath()
            );
            $detailsByFixers[$fixer->getInsightClass()] = $details;
        }

        $this->cache->set($cacheKey, $detailsByFixers);
    }
}
