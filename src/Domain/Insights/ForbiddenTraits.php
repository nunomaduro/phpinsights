<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenTraits extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return count($this->getDetails()) > 0;
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
        $traits = $this->collector->getTraits();
        $traits = array_flip($this->filterFilesWithoutExcluded(array_flip($traits)));

        return array_values(array_map(static function (string $name): Details {
            return Details::make()->setFile($name);
        }, $traits));
    }
}
