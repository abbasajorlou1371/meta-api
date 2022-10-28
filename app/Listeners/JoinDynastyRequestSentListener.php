<?php

namespace App\Listeners;

use App\Events\JoinDynastyRequestSent;
use App\Models\User;
use App\Notifications\JoinDynastyRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class JoinDynastyRequestSentListener implements ShouldQueue
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
     * @param JoinDynastyRequestSent $event
     * @return void
     */
    public function handle(JoinDynastyRequestSent $event)
    {
        $event->joinRequest->fromUser->notify(new JoinDynastyRequestNotification($event->joinRequest,$event->code));
    }
}
