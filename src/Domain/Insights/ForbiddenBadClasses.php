<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenBadClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return count($this->getDetails()) > 0;
    }

    public function getTitle(): string
    {
        return (string) ($this->config['title'] ?? 'Bad classes are forbidden.');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $badClasses = $this->collector->getBadClasses();
        $badClasses = array_flip($this->filterFilesWithoutExcluded(
            array_flip($badClasses)
        ));

        return array_map(static function (string $file): Details {
            return Details::make()->setFile($file);
        }, $badClasses);
    }
}
