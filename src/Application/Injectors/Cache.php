<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Injectors;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * @internal
 */
final class Cache
{
    /**
     * Inject Cache into the container definitions.
     *
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            CacheInterface::class => static fn (): CacheInterface => new Psr16Cache(
                new FilesystemAdapter('phpinsights')
            ),
        ];
    }
}
