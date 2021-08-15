<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenNormalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return $this->getDetails() !== [];
    }

    public function getTitle(): string
    {
        return (string) ($this->config['title'] ?? 'Normal classes are forbidden. Classes must be final or abstract');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $nonFinalClasses = $this->collector->getConcreteNonFinalClasses();
        $nonFinalClasses = array_flip($this->filterFilesWithoutExcluded(
            array_flip($nonFinalClasses)
        ));

        return array_map(static fn (string $file): Details => Details::make()->setFile($file), $nonFinalClasses);
    }
}
