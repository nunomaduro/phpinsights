<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Structure\Composer;

use NunoMaduro\PhpInsights\Domain\Insights\Insight;

/**
 * @internal
 */
final class ContainsName extends Insight
{
    /**
     * @var string[]
     */
    private $defaults = [
        'laravel/laravel'
    ];

    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        $contents = json_decode(ComposerFinder::contents($this->filesRepository), true);

        return ! array_key_exists('name', $contents) || array_key_exists($contents['name'], array_flip($this->defaults));
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'The name property in the `composer.json` contains the default value';
    }
}
