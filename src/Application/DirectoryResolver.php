<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class DirectoryResolver
{
    public static function resolve(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument('directory') ?? (string) getcwd();

        if ($directory[0] !== DIRECTORY_SEPARATOR && preg_match('~\A[A-Z]:(?![^/\\\\])~i', $directory) === 0) {
            $directory = (string) getcwd() . DIRECTORY_SEPARATOR . $directory;
        }

        return $directory;
    }
}
