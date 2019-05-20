<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\WordPress;

use NunoMaduro\PhpInsights\Domain\Contracts\Preset as PresetContract;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
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
            ],
            'remove'  => [
                TypeHintDeclarationSniff::class,
                \WooCommerce::class,
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
    public static function shouldBeApplied(array $composer): bool
    {
        /** @var string[] $requirements */
        $requirements = $composer['require'] ?? [];

        if ($requirements) {
            return self::composerDiscovery($requirements);
        }

        return self::manualInstallationDiscovery();
    }

    /**
     * Defining ways to discover WordPress through composer.
     *
     * @param  array  $requirements  Composer requirements list.
     *
     * @return bool
     */
    protected static function composerDiscovery(array $requirements): bool
    {
        foreach (array_keys($requirements) as $requirement) {
            $requirement = (string) $requirement;

            if (strpos($requirement, 'johnpbloch/wordpress') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * When manual installation is been use.
     *
     * @return bool
     */
    protected static function manualInstallationDiscovery(): bool
    {
        $finder = new Finder();

        $finder
            ->files()
            ->name(['wp-config.php', 'wp-settings.php'])
            ->notPath(['vendor', 'wp-admin', 'wp-includes']);

        return $finder->count() > 0;
    }
}
