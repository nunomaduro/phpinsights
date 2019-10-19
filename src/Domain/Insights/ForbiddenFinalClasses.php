<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenFinalClasses extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return (bool) count($this->getDetails());
    }

    public function getTitle(): string
    {
        return array_key_exists('title', $this->config) ? (string) $this->config['title'] : 'The use of `final` classes is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $concreteFinalClasses = array_filter(
            $this->collector->getConcreteFinalClasses(),
            function ($file) {
                return $this->shouldSkipFile($file) === false;
            },
            ARRAY_FILTER_USE_KEY
        );

        return array_map(static function (string $name): Details {
            return Details::make()->setFile($name);
        }, $concreteFinalClasses);
    }
}
