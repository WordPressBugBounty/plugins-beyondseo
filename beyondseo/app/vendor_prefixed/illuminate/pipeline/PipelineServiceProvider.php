<?php

namespace BeyondSEODeps\Illuminate\Pipeline;

use BeyondSEODeps\Illuminate\Contracts\Pipeline\Hub as PipelineHubContract;
use BeyondSEODeps\Illuminate\Contracts\Support\DeferrableProvider;
use BeyondSEODeps\Illuminate\Support\ServiceProvider;

class PipelineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            PipelineHubContract::class, Hub::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            PipelineHubContract::class,
        ];
    }
}
