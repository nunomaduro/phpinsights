<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenSecurityIssues;

/**
 * @internal
 */
final class Kernel
{
    /**
     * The app version.
     *
     * @noRector Rector\DeadCode\Rector\ClassConst\RemoveUnusedClassConstantRector
     */
    public const VERSION = 'v2.7.0';

    /**
     * Bootstraps the usage of the package.
     *
     * return void
     */
    public static function bootstrap(): void
    {
        /*
         * Includes PHP Codesniffer's autoload.
         */
        include_once file_exists(__DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php')
            ? __DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php'
            : __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php';

        /*
         * Defines PHP Codesniffer's needed constants.
         */
        if (! defined('PHP_CODESNIFFER_CBF')) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }

        /**
         * Require Tokens utils From PHP Codesniffer.
         */
        require_once file_exists(__DIR__ . '/../../vendor/squizlabs/php_codesniffer/src/Util/Tokens.php')
            ? __DIR__ . '/../../vendor/squizlabs/php_codesniffer/src/Util/Tokens.php'
            : __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/src/Util/Tokens.php';
    }

    /**
     * Returns the list of required files.
     *
     * @return array<string>
     */
    public static function getRequiredFiles(): array
    {
        return [
            'composer.json',
            'composer.lock',
            // '.gitignore',
        ];
    }

    /**
     * Returns the list of Insights required on root.
     *
     * @return array<string>
     */
    public static function getGlobalInsights(): array
    {
        return [
            ForbiddenSecurityIssues::class,
        ];
    }
}
