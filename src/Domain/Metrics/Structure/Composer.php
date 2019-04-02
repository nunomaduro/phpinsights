<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\Structure\Composer\ContainsName;
use NunoMaduro\PhpInsights\Domain\Insights\Structure\Composer\Exists;

/**
 * @internal
 */
final class Composer implements HasInsights
{
    /**
     * {@inheritDoc}
     */
    public function getInsights(): array
    {
        return [
            Exists::class,
            ContainsName::class,
            /**
             * UpToDateDependencies::class,
             * SpecifiesPhpVersion::class,
             * PrefersStable::class,
             * PsrAutoload::class,
             */
        ];
    }
}
