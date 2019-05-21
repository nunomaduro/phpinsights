<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

final class ForbiddenDefineFunctions extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return (bool) count($this->collector->getNamedFunctions());
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Defining global helpers is prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $namedFunctionsPerFile = $this->collector->getNamedFunctions();

        $details = [];
        foreach ($namedFunctionsPerFile as $file => $namedFunctions) {
            foreach ($namedFunctions as $key => $namedFunction) {
                $number = $key + 1;
                $details[] = "$file:{$number}:$namedFunction";
            }
        }

        return $details;
    }
}
