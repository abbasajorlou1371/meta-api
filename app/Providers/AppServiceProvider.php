<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('viewApiDocs', function () {
            return filter_var(config('scramble.enabled'), FILTER_VALIDATE_BOOLEAN);
        });

        // Standard Scramble docs: endpoints are grouped by their controller tag
        // (Controller > Endpoint). Exposed at the existing documentation URLs.
        Scramble::configure()
            ->expose(
                ui: 'api/documentation',
                document: 'docs/api-docs.json',
            );
    }
}
