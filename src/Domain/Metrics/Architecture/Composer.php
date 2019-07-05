<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerLockMustBeFresh;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustContainName;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustExist;

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
            ComposerMustBeValid::class,
            ComposerLockMustBeFresh::class,
        ];
    }
}
