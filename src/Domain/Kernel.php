<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

/**
 * @internal
 */
final class Kernel
{
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
    }

    /**
     * Returns the list of required files.
     *
     * @return string[]
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
