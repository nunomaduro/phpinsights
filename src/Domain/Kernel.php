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
        include_once 'vendor/squizlabs/php_codesniffer/autoload.php';
    }
}
