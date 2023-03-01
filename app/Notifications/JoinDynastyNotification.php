<?php

namespace App\Notifications;

use App\Mail\Dynasty\RecieverConfirmationMail;
use App\Mail\Dynasty\SenderConfirmationMail;
use App\Mail\RecieverAcceptMail;
use App\Mail\SenderAcceptMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JoinDynastyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return match ($this->data['type']) {
            'requester_confirmation_message' => (new SenderConfirmationMail($this->data['request']))->to($notifiable->email),
            'reciever_message' => (new RecieverConfirmationMail($this->data['request']))->to($notifiable->email),
            'requester_accept_message' => (new SenderAcceptMail($this->data['request']))->to($notifiable->email),
            'reciever_accept_message' => (new RecieverAcceptMail($this->data['request']))->to($notifiable->email),
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'related-to' => 'dynasty-join-requests',
            'sender-image' => 'https://dl.qzparadise.ir/public/metarang/logo.png',
            'sender-name' => 'متارنگ',
            'message' => $this->data['message']
        ];
    }
}
