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
     * @var array<string, string>
     */
    private static $formatters = [
        'console' => Console::class,
        'json' => Json::class,
        'checkstyle' => Checkstyle::class,
    ];

    public static function resolve(
        InputInterface $input,
        OutputInterface $output,
        OutputInterface $consoleOutput
    ): Formatter
    {
        $requestedFormat = $input->getOption('format');

        if (! is_string($requestedFormat)) {
            throw new InvalidArgumentException(
                "Format has to be a string."
            );
        }

        $requestedFormat = strtolower($requestedFormat);

        if (! array_key_exists($requestedFormat, self::$formatters)) {
            $consoleOutput->writeln("<fg=red>Could not find requested format [$requestedFormat], using fallback [console] instead.</>");
        }

        $formatter = self::$formatters[$requestedFormat] ?? Console::class;

        return new $formatter($input, $output);
    }

}
