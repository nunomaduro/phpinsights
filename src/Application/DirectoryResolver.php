<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class DirectoryResolver
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string>
     */
    public static function resolve(InputInterface $input): array
    {
        /** @var array<string> $directories */
        $directories = $input->getArgument('directory');

        if ($directories === [] || $directories == null) {
            $directories = [(string) getcwd()];
        }

        $directoryList = [];
        foreach ($directories as $directory) {
            $directory[0] !== DIRECTORY_SEPARATOR && preg_match('~\A[A-Z]:(?![^/\\\\])~i', $directory) === 0
                ? $directoryList[] = getcwd() . DIRECTORY_SEPARATOR . $directory
                : $directoryList[] = $directory;
        }

        return $directoryList;
    }
}
