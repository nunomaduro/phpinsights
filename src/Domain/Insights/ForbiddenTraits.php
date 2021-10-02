<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

/**
 * @see \Tests\Domain\Insights\ForbiddenTraitsTest
 */
final class ForbiddenTraits extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return $this->getDetails() !== [];
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

        return array_values(array_map(
            static fn (string $name): Details => Details::make()->setFile($name),
            $traits
        ));
    }
}
