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
            if (class_exists($requestedFormat)) {
                $formatters[] = new $requestedFormat($input, $output);
                continue;
            }

            $requestedFormat = strtolower($requestedFormat);

            if (! array_key_exists($requestedFormat, self::$formatters)) {
                $consoleOutput->writeln("<fg=red>Could not find requested format [{$requestedFormat}], using fallback [console] instead.</>");
            }

            $formatter = self::$formatters[$requestedFormat] ?? Console::class;
            $formatters[] = new $formatter($input, $output);
        }
        return new Multiple($formatters);
    }
}
