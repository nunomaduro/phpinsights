<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\WordPress;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenUsingGlobals;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;
use Symfony\Component\Finder\Finder;

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
        return 'wordpress';
    }

    /**
     * {@inheritDoc}
     */
    public static function get(): array
    {
        return [
            'exclude' => [
                'wp-admin',
                'wp-includes',
                'index.php',
            ],
            'remove'  => [
                AlphabeticallySortedUsesSniff::class,
                DeclareStrictTypesSniff::class,
                ForbiddenDefineFunctions::class,
                TypeHintDeclarationSniff::class,
                ForbiddenUsingGlobals::class
            ],
            'config'  => [
                ForbiddenFunctionsSniff::class => [
                    'forbiddenFunctions' => [
                        'var_dump' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function shouldBeApplied(array $composer, string $directory): bool
    {
        /** @var array<string, string> $requirements */
        $requirements = $composer['require'] ?? [];

        if (count($requirements) > 0) {
            return self::composerDiscovery($requirements);
        }

        return self::manualInstallationDiscovery($directory);
    }

    /**
     * Defining ways to discover WordPress through composer.
     *
     * @param  array<string, string>  $requirements  Composer requirements list.
     *
     * @return bool
     */
    private static function composerDiscovery(array $requirements): bool
    {
        foreach (array_keys($requirements) as $requirement) {

            if (strpos($requirement, 'johnpbloch/wordpress') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * When manual installation is been use.
     *
     * @param string  $directory
     *
     * @return bool
     */
    private static function manualInstallationDiscovery(string $directory): bool
    {
        $finder = new Finder();

        $finder
            ->in($directory)
            ->files()
            ->name('wp-load.php');

        return $finder->count() > 0;
    }
}
