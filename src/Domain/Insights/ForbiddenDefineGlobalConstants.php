<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenDefineGlobalConstants extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return count($this->getDetails()) > 0;
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
        $globalConstants = array_filter($globalConstants, function ($key) {
            return $this->shouldSkipFile($key) === false;
        }, ARRAY_FILTER_USE_KEY);

        return array_map(static function ($file, $constant): Details {
            return Details::make()
                ->setFile($file)
                ->setMessage($constant);
        }, array_keys($globalConstants), $globalConstants);
    }
}
