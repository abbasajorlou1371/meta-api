<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynastyJoinRequestSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $senderMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderMessage)
    {
        $this->senderMessage = $senderMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.dynasty-join-request-sent')
            ->from('metargb@test.com','متارنگ')
            ->subject('متارنگ - افزایش سلسله');
    }
}
