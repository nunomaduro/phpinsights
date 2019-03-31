<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\PublisherRepository;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class LocalPublisherRepository implements PublisherRepository
{
    /**
     * {@inheritdoc}
     */
    public function get(array $dirs): Publisher
    {
        ($analyser = new Analyser())->countFiles($dirs, true);

        return $analyser->getCollector()->getPublisher();
    }
}
