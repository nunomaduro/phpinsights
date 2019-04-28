<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Metrics\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure;

/**
 * @internal
 */
final class MetricsFinder
{
    /**
     * @return string[]
     */
    public static function find(): array
    {
        return [
            Code\Classes::class,
            Code\Code::class,
            Code\Comments::class,
            Code\Functions::class,
            Code\Globally::class,
            Complexity\Complexity::class,
            Structure\Composer::class,
            Structure\Constants::class,
            Structure\Functions::class,
            Structure\Files::class,
            Structure\Classes::class,
            Structure\Interfaces::class,
            Structure\Traits::class,
            Dependencies\Globally::class,
            Dependencies\Dependencies::class,
        ];
    }
}
