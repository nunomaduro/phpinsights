<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Laravel;

use NunoMaduro\PhpInsights\Domain\Insights\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;
use RuntimeException;


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
        } catch (RuntimeException $e) {
            return true;
        }

        return ! array_key_exists('require', $contents)
            || ! array_key_exists('laravel/framework', $contents['require'])
            || strpos('5.8.*', $contents['require']['laravel/framework']) === false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Your laravel version is outdated: Consider update your project to Laravel 5.8';
    }
}
