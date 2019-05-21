<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;

final class ComposerMustContainName extends Insight
{
    /**
     * @var string[]
     */
    private $defaults = [
        'laravel/laravel',
        'symfony/symfony',
    ];

    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        try {
            $contents = json_decode(
                ComposerFinder::contents($this->collector),
                true
            );
        } catch (ComposerNotFound $e) {
            return true;
        }

        return array_key_exists('name', $contents) && array_key_exists(
                $contents['name'],
                array_flip($this->defaults)
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'The name property in the `composer.json` contains the default value';
    }
}
