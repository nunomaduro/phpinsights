<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\ComposerFinder;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerMustExist extends Insight
{
    public function hasIssue(): bool
    {
        try {
            ComposerFinder::contents($this->collector);
        } catch (ComposerNotFound $e) {
            return true;
        }

        return false;
    }

    public function getTitle(): string
    {
        return 'The `composer.json` file was not found';
    }
}
