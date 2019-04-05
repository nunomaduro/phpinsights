<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Structure\Composer;

use NunoMaduro\PhpInsights\Domain\Insights\Insight;

/**
 * @internal
 */
final class Exists extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        try {
            ComposerFinder::contents($this->collector);
        } catch (\RuntimeException $e) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'The `composer.json` file was not found';
    }
}
