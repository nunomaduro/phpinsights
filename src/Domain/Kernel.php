<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use RuntimeException;

/**
 * @internal
 */
final class Kernel
{
    /**
     * Bootstraps the usage of the package.
     *
     * @param  string  $dir
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