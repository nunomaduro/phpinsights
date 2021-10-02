<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Details;

final class ForbiddenDefineFunctions extends Insight implements HasDetails
{
    public function hasIssue(): bool
    {
        return $this->getDetails() !== [];
    }

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
            if ($this->shouldSkipFile($file)) {
                continue;
            }

            foreach ($namedFunctions as $key => $namedFunction) {
                $number = $key + 1;
                $details[] = Details::make()
                    ->setFile($file)
                    ->setLine($number)
                    ->setFunction($namedFunction);
            }
        }

        return $details;
    }
}
