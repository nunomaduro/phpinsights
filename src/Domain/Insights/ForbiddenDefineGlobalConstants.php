<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenDefineGlobalConstants extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return $this->getDetails() !== [];
    }

    public function getTitle(): string
    {
        return 'Define `globals` is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        /** @var array<string> $ignore */
        $ignore = $this->config['ignore'] ?? [];

        $globalConstants = array_diff($this->collector->getGlobalConstants(), $ignore);
        $globalConstants = $this->filterFilesWithoutExcluded($globalConstants);

        return array_map(static fn ($file, $constant): Details => Details::make()
            ->setFile($file)
            ->setMessage($constant), array_keys($globalConstants), $globalConstants);
    }
}
