<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenNormalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return count($this->getDetails()) > 0;
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
        $nonFinalClasses = array_filter(
            $this->collector->getConcreteNonFinalClasses(),
            function ($file) {
                return $this->shouldSkipFile($file) === false;
            }
        );

        return array_map(static function (string $file): Details {
            return Details::make()->setFile($file);
        }, $nonFinalClasses);
    }
}
