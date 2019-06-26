<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenNormalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getConcreteNonFinalClasses());
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
        return array_map(static function (string $file): Details {
            return Details::make()->withFile($file);
        }, $this->collector->getConcreteNonFinalClasses());
    }
}
