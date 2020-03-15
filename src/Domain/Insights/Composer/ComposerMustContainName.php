<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerMustContainName extends Insight
{
    /**
     * @var array<string>
     */
    private $defaults = [
        'laravel/laravel',
        'symfony/symfony',
    ];

    /**
     * @var bool
     */
    private $analyzed = false;
    /**
     * @var bool
     */
    private $hasError = false;

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
            $contents = json_decode(ComposerFinder::contents($this->collector), true);
            $this->hasError = array_key_exists('name', $contents) && array_key_exists($contents['name'], array_flip($this->defaults));
        } catch (ComposerNotFound $e) {
            $this->hasError = true;
        }

        $this->analyzed = true;
    }
}
