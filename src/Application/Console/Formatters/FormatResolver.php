<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

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
        'github-action' => GithubAction::class,
    ];

    public static function resolve(
        InputInterface $input,
        OutputInterface $output,
        OutputInterface $consoleOutput
    ): Formatter {
        $requestedFormats = $input->getOption('format');

        if (! is_array($requestedFormats)) {
            $consoleOutput->writeln('<fg=red>Could not understand requested format, using fallback [console] instead.</>');
            $requestedFormats = ['console'];
        }

        $formatters = [];
        foreach ($requestedFormats as $requestedFormat) {
            $formatters[] = self::stringToFormatter(
                $requestedFormat,
                $input,
                $output,
                $consoleOutput
            );
        }
        return new Multiple($formatters);
    }

    private static function stringToFormatter(
        string $requestedFormat,
        InputInterface $input,
        OutputInterface $output,
        OutputInterface $consoleOutput
    ): Formatter {
        if (class_exists($requestedFormat)) {
            $class = $requestedFormat;
        }

        if (array_key_exists($requestedFormat, self::$formatters)) {
            $class = self::$formatters[strtolower($requestedFormat)];
        }

        if (!isset($class) || !($class instanceof Formatter)) {
            $consoleOutput->writeln("<fg=red>Could not find requested format [{$requestedFormat}], using fallback [console] instead.</>");
            $class = Console::class;
        }

        return $class($input, $output);
    }
}
