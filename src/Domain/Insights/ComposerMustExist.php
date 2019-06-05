<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;

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
