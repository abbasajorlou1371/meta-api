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

        Scramble::configure()
            ->expose(
                ui: config('scramble.expose.ui'),
                document: config('scramble.expose.document'),
            );
    }
}
