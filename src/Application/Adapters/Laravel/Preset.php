<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenPrivateMethods;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Insights\Laravel\ComposerCheckLaravelVersion;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure\ClassesFinal;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure\Composer;
use NunoMaduro\PhpInsights\Domain\Metrics\Structure\MethodsPrivate;

/**
 * @internal
 */
final class Preset implements PresetContract
{
    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        return [
            'add' => [
                MethodsPrivate::class => [
                    ForbiddenPrivateMethods::class,
                ],
                ClassesFinal::class => [
                    ForbiddenFinalClasses::class,
                ],
                Composer::class => [
                    ComposerCheckLaravelVersion::class,
                ]
            ],
            'remove' => [
                ForbiddenTraits::class,
                ForbiddenDefineFunctions::class,
                ForbiddenNormalClasses::class,
            ],
            'config' => [
                ForbiddenPrivateMethods::class => [
                    'title' => 'The usage of private methods is not idiomatic in Laravel.',
                ],
                ForbiddenDefineGlobalConstants::class => [
                    'ignore' => ['LARAVEL_START'],
                ],
            ],
        ];
    }
}
