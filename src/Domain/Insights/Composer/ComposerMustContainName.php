<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerMustContainName extends Insight
{
    private const DEFAULTS = [
        'laravel/laravel',
        'symfony/symfony',
    ];

    private bool $analyzed = false;

    private bool $hasError = false;

    public function hasIssue(): bool
    {
        if (! $this->analyzed) {
            $this->check();
        }

        return $this->hasError;
    }

    public function getTitle(): string
    {
        return 'The name property in the `composer.json` contains the default value';
    }

    private function check(): void
    {
        try {
            $contents = json_decode(ComposerFinder::contents($this->collector), true, 512, JSON_THROW_ON_ERROR);
            $this->hasError = array_key_exists('name', $contents)
                && array_key_exists($contents['name'], array_flip(self::DEFAULTS));
        } catch (ComposerNotFound $e) {
            $this->hasError = true;
        }

        $this->analyzed = true;
    }
}
