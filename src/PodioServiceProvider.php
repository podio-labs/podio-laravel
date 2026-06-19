<?php

namespace Podio\Laravel;

use Illuminate\Support\ServiceProvider;

final class PodioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/podio.php', 'podio');

        $this->app->singleton(Podio::class);
    }

    public function boot(): void
    {
        $this->publishes(
            paths: [
                __DIR__ . '/../config/podio.php' => config_path('podio.php'),
            ],
            groups: 'podio-config'
        );
    }
}
