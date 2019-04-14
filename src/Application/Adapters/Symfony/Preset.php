<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Symfony;

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
    public static function getName(): string
    {
        return 'symfony';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'var',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var string[] $requirements */
        $requirements = $composer['require'];

        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;

            if (strpos($requirement, 'symfony/framework-bundle') !== false
                || strpos($requirement, 'symfony/flex') !== false
                || strpos($requirement, 'symfony/symfony') !== false) {
                return true;
            }
        }

        return false;
    }
}
