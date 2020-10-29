<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Formatters;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Container;
use NunoMaduro\PhpInsights\Domain\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;

/**
 * @internal
 */
final class FormatResolver
{
    public const FORMATTERS = [
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
        $requestedFormats = !empty($input->getOption('format')) ? $input->getOption('format') : Container::make()->get(Configuration::class)->getFormats();

        if (! is_array($requestedFormats)) {
            $consoleOutput->writeln(
                '<fg=red>Could not understand requested format, using fallback [console] instead.</>'
            );

            $requestedFormats = ['console'];
        }

        $formatters = [];
        foreach ($requestedFormats as $requestedFormat) {
            try {
                $formatter = self::stringToFormatterClass($requestedFormat);

                $instance = new $formatter($input, $output);

                if (! ($instance instanceof Formatter)) {
                    $consoleOutput->writeln(
                        "<fg=red>The formatter [{$formatter}] is not implementing the interface.</>"
                    );

                    continue;
                }

                $formatters[] = $instance;
            } catch (InvalidArgumentException $exception) {
                $consoleOutput->writeln("<fg=red>Could not find requested format [{$requestedFormat}].</>");
            }
        }

        if ($formatters === []) {
            $consoleOutput->writeln(
                '<fg=red>No requested formats were found, using fallback [console] instead.</>'
            );

            return new Console($input, $output);
        }

        return new Multiple($formatters);
    }
    private static function stringToFormatterClass(string $requestedFormat): string
    {
        if (class_exists($requestedFormat)) {
            return $requestedFormat;
        }

        if (array_key_exists($requestedFormat, self::FORMATTERS)) {
            return self::FORMATTERS[strtolower($requestedFormat)];
        }

        throw new InvalidArgumentException('Could not find a formatter from string.');
    }
}
