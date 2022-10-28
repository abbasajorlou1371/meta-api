<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynastyJoinRequestAccepted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $joinRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.dynasty-join-request-accepted')
            ->from('metargb@gmail.com','متارنگ')
            ->subject('متارنگ - افزایش سلسله');;
    }
}
