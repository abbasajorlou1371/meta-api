<?php

namespace App\Listeners;

use App\Events\FeatureTraded;
use App\Notifications\BuyFeatureNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FeatureTradedListener
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FeatureTraded $event)
    {
        $event->trade->buyer->notify(new BuyFeatureNotification($event->trade));
    }
}
