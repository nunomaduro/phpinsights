<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Contracts\Repositories;

use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFoundException;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
interface PublisherRepository
{
    /**
     * Get the publisher from the given dirs.
     *
     * @param  array  $dirs
     *
     * @return \NunoMaduro\PhpInsights\Domain\Publisher
     *
     * @throws DirectoryNotFoundException
     */
    public function get(array $dirs): Publisher;
}
