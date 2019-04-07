<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use RuntimeException as RuntimeExceptionAlias;

/**
 * @internal
 */
final class ComposerContainsName extends Insight
{
    /**
     * @var string[]
     */
    private $defaults = [
        'laravel/laravel',
    ];

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
