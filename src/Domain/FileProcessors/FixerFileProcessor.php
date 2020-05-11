<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use LogicException;
use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
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

    public function __construct(DifferInterface $differ)
    {
        $this->differ = $differ;
        $this->fixEnabled = Container::make()->get(Configuration::class)->hasFixEnabled();
        $this->cache = Container::make()->get(CacheInterface::class);
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
        $needFix = false;

        try {
            $tokens = @Tokens::fromCode($oldContent);
            $originalTokens = clone $tokens;
            /** @var FixerDecorator $fixer */
            foreach ($this->fixers as $fixer) {
                $cacheKey = md5($oldContent) . '.fixer.' . $fixer->getName();

                if ($this->cache->get($cacheKey, '') !== '') {
                    $fixer->addDiff($filePath, $this->cache->get($cacheKey));
                }

                if (! $this->fixEnabled && $this->cache->has($cacheKey)) {
                    continue;
                }

                $fixer->fix($splFileInfo, $tokens);

                if (! $tokens->isChanged()) {
                    $this->cache->set($cacheKey, '');
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

                $diff = $this->differ->diff($oldContent, $tokens->generateCode());
                $this->cache->set($cacheKey, $diff);

                $fixer->addDiff($filePath, $diff);
                // Tokens has changed, so we need to clear cache
                Tokens::clearCache();
                $tokens = clone $originalTokens;
            }

            if (! $this->fixEnabled || ! $needFix) {
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
}
