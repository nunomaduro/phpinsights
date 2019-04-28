<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Metrics\Architecture;
use NunoMaduro\PhpInsights\Domain\Metrics\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Dependencies;
use NunoMaduro\PhpInsights\Domain\Metrics\Style;

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
            Architecture\Classes::class,
            Architecture\Composer::class,
            Architecture\Constants::class,
            Architecture\Functions::class,
            Architecture\Files::class,
            Architecture\Methods::class,
            Architecture\Classes::class,
            Architecture\Interfaces::class,
            Architecture\Traits::class,
            Dependencies\Globally::class,
            Dependencies\Dependencies::class,
            Style\Style::class,
        ];
    }
}
