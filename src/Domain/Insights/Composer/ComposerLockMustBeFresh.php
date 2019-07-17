<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights\Composer;

use NunoMaduro\PhpInsights\Domain\ComposerLoader;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;
use NunoMaduro\PhpInsights\Domain\Exceptions\ComposerNotFound;
use NunoMaduro\PhpInsights\Domain\Insights\Insight;

final class ComposerLockMustBeFresh extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        try {
            $composer = ComposerLoader::getInstance($this->collector);

            return ! $composer->getLocker()->isFresh();
        } catch (ComposerNotFound $exception) {
            return true;
        }
    }

    public function getTitle(): string
    {
        return 'The lock file is not up to date with the latest changes in composer.json';
    }

    public function getDetails(): array
    {
        return [
            Details::make()
                ->setMessage('You may be getting outdated dependencies. Run update to update them.'),
        ];
    }
}
