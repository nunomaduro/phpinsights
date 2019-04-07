<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use RuntimeException as RuntimeExceptionAlias;

/**
 * @internal
 */
final class ComposerCheckLaravelVersion extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        try {
            $contents = json_decode(ComposerFinder::contents($this->collector), true);
        } catch (RuntimeExceptionAlias $e) {
            return true;
        }

        return strpos('5.8.*', $contents['require']['laravel/framework']) === false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Your laravel version is outdated.';
    }
}
