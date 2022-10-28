<?php

namespace App\Listeners;

use App\Events\FeaturePriced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\SellRequestNotification;

class FeaturePricedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FeaturePriced $event)
    {
        $event->sellRequest->seller->notify(
            new SellRequestNotification($event->sellRequest->feature)
        );
    }
}
