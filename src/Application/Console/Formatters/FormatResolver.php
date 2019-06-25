<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Application\Console\Style;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class FormatResolver
{
    /**
     * @var array<string>
     */
    private static $formatters = [
        'console' => Console::class,
        'json' => Json::class,
    ];

    private static $default = Console::class;

    public static function resolve(
        InputInterface $input,
        OutputInterface $output
    ): Formatter
    {
        $requestedFormat = strtolower($input->getOption('format'));

        $formatter = self::$formatters[$requestedFormat] ?? self::$default;

        return new $formatter($input, $output);
    }

}
