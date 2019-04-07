<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use Illuminate\Support\ServiceProvider;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands\InsightsCommand;
use NunoMaduro\PhpInsights\Application\Injectors\Repositories;

/**
 * @internal
 */
final class InsightsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        foreach ($this->app->make(Repositories::class)->__invoke() as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../../stubs/config.php' => $this->app->configPath('insights.php'),
        ], 'config');

        $this->commands([
            InsightsCommand::class,
        ]);
    }
}
