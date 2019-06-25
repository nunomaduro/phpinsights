<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
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

    public static function resolve(
        InputInterface $input,
        OutputInterface $output
    ): Formatter
    {
        $requestedFormat = $input->getOption('format');

        if (! is_string($requestedFormat)) {
            throw new InvalidArgumentException(
                "Format has to be a string."
            );
        }

        $requestedFormat = strtolower($requestedFormat);

        $formatter = self::$formatters[$requestedFormat] ?? Console::class;

        return new $formatter($input, $output);
    }

}
