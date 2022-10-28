<?php

namespace App\Listeners;

use App\Events\LogedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Level\UserActivity;
use App\Notifications\LogedInNotification;

class LogedInListener
{
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
    public function handle(LogedIn $event)
    {
        $event->user->notify(new LogedInNotification($event->user->ip));
        $event->user->activities()->create([
            'start' => now(),
            'ip' => $event->user->ip,
        ]);
    }
}
