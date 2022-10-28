<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FamilyMemeberAcceptedJoinRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $joinRequest, $fromUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($joinRequest, $fromUser)
    {
        $this->joinRequest = $joinRequest;
        $this->fromUser = $fromUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.family-member-accepted-join-request')
            ->subject('متارنگ - پذیرش درخواست ورود به سلسله')
            ->from('metarang@gmail.com', 'متارنگ');
    }
}
