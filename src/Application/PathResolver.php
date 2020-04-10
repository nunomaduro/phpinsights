<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application;

use Symfony\Component\Console\Input\InputInterface;

/**
 * @internal
 */
final class PathResolver
{
    /**
     * @return array<string>
     */
    public static function resolve(InputInterface $input): array
    {
        /** @var array<string>|null $paths */
        $paths = $input->getArgument('paths');

        if ($paths === [] || $paths === null) {
            $paths = [(string) getcwd()];
        }

        $pathList = [];
        foreach ($paths as $path) {
            $pathList[] = $path[0] !== DIRECTORY_SEPARATOR && preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) === 0
                ? getcwd() . DIRECTORY_SEPARATOR . $path
                : $path;
        }

        return $pathList;
    }
}
