<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerMustExist extends Insight
{
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
        return 'The `composer.json` file was not found';
    }

    private function check(): void
    {
        try {
            ComposerFinder::contents($this->collector);
        } catch (ComposerNotFound $e) {
            $this->hasError = true;
        }
        $this->analyzed = true;
    }
}
