<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenDefineGlobalConstants extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        /** @var array<string> $ignore */
        $ignore = $this->config['ignore'] ?? [];

        return count(array_diff($this->collector->getGlobalConstants(), $ignore)) > 0;
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

        return array_map(static function ($file, $constant) {
            return "$file: $constant";
        }, array_keys($globalConstants), $globalConstants);
    }
}
