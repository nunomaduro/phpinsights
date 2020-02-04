<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class Repositories
{
    /**
     * Injects repositories into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            FilesRepository::class => static function (): LocalFilesRepository {
                $finder = Finder::create();

                return new LocalFilesRepository($finder);
            },
        ];
    }
}
