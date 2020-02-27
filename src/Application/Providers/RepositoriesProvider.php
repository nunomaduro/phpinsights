<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Infrastructure\Repositories\LocalFilesRepository;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class RepositoriesProvider extends AbstractServiceProvider
{
    protected $provides = [
        FilesRepository::class,
    ];

    public function register()
    {
        $this->getContainer()->add(
            FilesRepository::class, LocalFilesRepository::class
        )->addArgument(Finder::create());
    }
}
