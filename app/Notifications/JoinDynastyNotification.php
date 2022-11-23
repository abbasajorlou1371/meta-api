<?php

namespace App\Notifications;

use App\Mail\Dynasty\RecieverConfirmationMail;
use App\Mail\Dynasty\SenderConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JoinDynastyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $data;

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
        switch ($this->data['type']) {
            case 'requester_confirmation_message':
                return (new SenderConfirmationMail($this->data['message']))
                    ->from('rgb@gmail.com', 'متارنگ')
                    ->to($notifiable->email)
                    ->subject('درخواست پیوستن به سلسله');
                break;
            case 'reciever_confirmation_message':
                return (new RecieverConfirmationMail($this->data['message']))
                    ->from('rgb@gmail.com')
                    ->to($notifiable->email)
                    ->subject('تایید پذیرش پیوستن به سلسله');
                break;
            default:
                return [];
        }
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
            $this->data['message']
        ];
    }
}
