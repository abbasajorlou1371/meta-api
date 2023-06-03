<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendVerificationCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $phone, $code;

    public function __construct($reset)
    {
        $this->phone = $reset->phone;
        $this->code = $reset->code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable) {
        return [
            'phone' => $this->phone,
            'token' => $this->code,
            'template' => 'verify'
        ];
    }
}
