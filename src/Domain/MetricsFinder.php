<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Metrics\Architecture;
use NunoMaduro\PhpInsights\Domain\Metrics\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Security;
use NunoMaduro\PhpInsights\Domain\Metrics\Style;

/**
 * @internal
 */
final class MetricsFinder
{
    /**
     * @return array<string>
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
            Architecture\Files::class,
            Architecture\Functions::class,
            Architecture\Globally::class,
            Architecture\Interfaces::class,
            Architecture\Namespaces::class,
            Architecture\Traits::class,
            Style\Style::class,
            Security\Security::class,
        ];
    }
}
