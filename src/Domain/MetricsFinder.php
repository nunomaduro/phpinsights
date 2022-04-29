<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes as Architecture_Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Constants as Architecture_Constants;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files as Architecture_Files;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Functions as Architecture_Functions;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Globally as Architecture_Globally;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Interfaces as Architecture_Interfaces;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Namespaces as Architecture_Namespaces;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits as Architecture_Traits;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes as Code_Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Code as Code_Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments as Code_Comments;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions as Code_Functions;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally as Code_Globally;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity as Complexity_Complexity;
use NunoMaduro\PhpInsights\Domain\Metrics\Security\Security as Security_Security;
use NunoMaduro\PhpInsights\Domain\Metrics\Style\Style as Style_Style;

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
            Code_Classes::class,
            Code_Code::class,
            Code_Comments::class,
            Code_Functions::class,
            Code_Globally::class,
            Complexity_Complexity::class,
            Architecture_Classes::class,
            Architecture_Constants::class,
            Architecture_Files::class,
            Architecture_Functions::class,
            Architecture_Globally::class,
            Architecture_Interfaces::class,
            Architecture_Namespaces::class,
            Architecture_Traits::class,
            Style_Style::class,
            Security_Security::class,
        ];
    }
}
