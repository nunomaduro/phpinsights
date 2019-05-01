<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\ComposerMustContainName;
use NunoMaduro\PhpInsights\Domain\Insights\ComposerMustExist;

final class Composer implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            ComposerMustExist::class,
            ComposerMustContainName::class,
        ];
    }
}
