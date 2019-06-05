<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

/**
 * @internal
 */
final class Kernel
{
    /**
     * The app version.
     */
    public const VERSION = 'v1.6.0';

    /**
     * Bootstraps the usage of the package.
     *
     * return void
     */
    public static function bootstrap(): void
    {
        /**
         * Includes PHP Codesniffer's autoload.
         */
        include_once file_exists(__DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php')
            ? __DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php'
            : __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php';

        /**
         * Defines PHP Codesniffer's needed constants
         */
        if (! defined('PHP_CODESNIFFER_CBF')) {
            define('PHP_CODESNIFFER_CBF', false);
        }
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
}
