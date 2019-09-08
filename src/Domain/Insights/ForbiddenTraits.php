<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenTraits extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return count($this->collector->getTraits()) > 0;
    }

    public function getTitle(): string
    {
        return 'The use of `traits` is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return array_map(static function (string $name): Details {
            return Details::make()->setFile($name);
        }, $this->collector->getTraits());
    }
}
