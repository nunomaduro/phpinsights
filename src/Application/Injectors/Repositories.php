<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\PublisherRepository;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\GitRepository;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\RemoteGitRepository;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalPublisherRepository;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class Repositories
{
    /**
     * Injects repositories into the container definitions.
     *
     * @return mixed[]
     */
    public function __invoke(): array
    {
        return [
            PublisherRepository::class => LocalPublisherRepository::class,
            FilesRepository::class => function () {
                $finder = Finder::create();

                return new LocalFilesRepository($finder);
            },
        ];
    }
}
