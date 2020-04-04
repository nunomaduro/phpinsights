<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

use NunoMaduro\PhpInsights\Domain\Configuration;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Contracts\FileProcessor;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Differ;
use NunoMaduro\PhpInsights\Domain\Insights\FixerDecorator;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FixerFileProcessor implements FileProcessor
{
    /**
     * @var array<FixerDecorator>
     */
    private $fixers = [];

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Differ
     */
    private $differ;
    /**
     * @var bool
     */
    private $fixEnabled;

    public function __construct(
        Differ $differ
    ) {
        $this->differ = $differ;
        $this->fixEnabled = Container::make()->get(Configuration::class)->hasFixEnabled();
    }

    public function support(InsightContract $insight): bool
    {
        return $insight instanceof FixerDecorator;
    }

    public function addChecker(InsightContract $insight): void
    {
        if (! $insight instanceof FixerDecorator) {
            throw new \RuntimeException(sprintf(
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
            throw new \LogicException('Unable to found file ' . $splFileInfo->getFilename());
        }

        $oldContent = $splFileInfo->getContents();
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

                if ($this->fixEnabled === true) {
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

            if ($this->fixEnabled === false || $needFix === false) {
                return;
            }

            $tokens = @Tokens::fromCode($oldContent);
            // Reloop on fixer to get full tokens to change
            foreach ($this->fixers as $fixer) {
                $fixer->fix($splFileInfo, $tokens);
            }

            file_put_contents($splFileInfo->getPathname(), $tokens->generateCode());
        } catch (\Throwable $e) {
            // @ignoreException
        }
    }
}
