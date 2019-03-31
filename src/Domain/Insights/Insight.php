<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\Insight as InsightContract;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
abstract class Insight implements InsightContract
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    protected $filesRepository;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Collector
     */
    protected $collector;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Publisher
     */
    protected $publisher;

    /**
     * Creates an new instance of the Insight.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     * @param  \NunoMaduro\PhpInsights\Domain\Publisher  $publisher
     */
    final public function __construct(FilesRepository $filesRepository, Collector $collector, Publisher $publisher)
    {
        $this->filesRepository = $filesRepository;
        $this->collector = $collector;
        $this->publisher = $publisher;
    }
}
