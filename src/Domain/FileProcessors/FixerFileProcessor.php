<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\FileProcessors;

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

    public function __construct(
        Differ $differ
    ) {
        $this->differ = $differ;
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

        try {
            $tokens = @Tokens::fromCode($oldContent);
            /** @var FixerDecorator $fixer */
            foreach ($this->fixers as $fixer) {
                $fixer->fix($splFileInfo, $tokens);
                if (! $tokens->isChanged()) {
                    continue;
                }

                $fixer->addDiff(
                    $filePath,
                    $this->differ->diff($oldContent, $tokens->generateCode())
                );
                // Tokens has changed, so we need to clear cache
                Tokens::clearCache();
                $tokens = @Tokens::fromCode($oldContent);
            }
        } catch (\Throwable $e) {
            // @ignoreException
        }
    }
}
