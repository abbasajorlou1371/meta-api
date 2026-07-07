<?php

namespace App\Providers;

use App\Http\Controllers\ApiDocsController;
use App\Support\ApiDocumentation\ServiceTagResolver;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Router;
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

        Scramble::resolveTagsUsing(app(ServiceTagResolver::class));

        // Custom routes render the spec through ApiDocsController, which injects
        // `x-tagGroups` (Service > Controller grouping) not supported by Scramble's
        // OpenAPI object model.
        Scramble::configure()
            ->expose(
                ui: fn (Router $router, $action) => $router->get('api/documentation', [ApiDocsController::class, 'ui']),
                document: fn (Router $router, $action) => $router->get('docs/api-docs.json', [ApiDocsController::class, 'document']),
            );
    }
}
