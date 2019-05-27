<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Laravel;

use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerCheckLaravelVersion extends Insight
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        try {
            $composer = json_decode(
                ComposerFinder::contents($this->collector),
                true
            );
        } catch (ComposerNotFound $exception) {
            return true;
        }

        return array_key_exists('require', $composer)
            && array_key_exists('laravel/framework', $composer['require'])
            && false === strpos(
                $composer['require']['laravel/framework'],
                '5.8.*'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Your laravel version is outdated: Consider update your project to Laravel 5.8';
    }
}
