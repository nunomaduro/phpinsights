<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use NunoMaduro\PhpInsights\Domain\Contracts\InsightLoader;
use NunoMaduro\PhpInsights\Domain\InsightLoader\FixerLoader;
use NunoMaduro\PhpInsights\Domain\InsightLoader\PhpStanRuleLoader;
use NunoMaduro\PhpInsights\Domain\InsightLoader\SniffLoader;

/**
 * @internal
 */
final class InsightLoadersProvider extends AbstractServiceProvider
{
    /** @var array<class-string|string> */
    protected $provides = [
        SniffLoader::class,
        FixerLoader::class,
        PhpStanRuleLoader::class,
        InsightLoader::INSIGHT_LOADER_TAG,
    ];

    public function register(): void
    {
        $this->getLeagueContainer()->add(SniffLoader::class)
            ->addTag(InsightLoader::INSIGHT_LOADER_TAG);

        $this->getLeagueContainer()->add(PhpStanRuleLoader::class)
            ->addArgument($this->getContainer())
            ->addTag(InsightLoader::INSIGHT_LOADER_TAG);

        $this->getLeagueContainer()->add(FixerLoader::class)
            ->addTag(InsightLoader::INSIGHT_LOADER_TAG);
    }
}
