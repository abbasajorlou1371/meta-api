<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes([
            'prefix' => 'api/public',
            'middlware' => ['api']
        ]);
        Broadcast::routes([
            'prefix' => 'api',
            'middleware' => ['auth:sanctum', 'api']
        ]);

        require base_path('routes/channels.php');
    }
}
