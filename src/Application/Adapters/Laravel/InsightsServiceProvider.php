<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Adapters\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\PhpInsights\Application\Adapters\Laravel\Commands\InsightsCommand;
use NunoMaduro\PhpInsights\Application\Injectors\Repositories;

/**
 * @internal
 */
final class InsightsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->app->make(Repositories::class)->__invoke() as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    public function boot(): void
    {
        if ($this->app instanceof Application) {
            $this->publishes([
                __DIR__.'/../../../../stubs/laravel.php' => $this->app->configPath('insights.php'),
            ], 'config');
        }

        $this->commands([
            InsightsCommand::class,
        ]);
    }
}
