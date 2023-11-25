<?php

namespace App\Notifications;

use App\Mail\logedInMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LogedInNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return array_keys(array_filter($notifiable->getNotificationSettings('login_verification'), function ($key) {
            return $key;
        }));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new logedInMail($notifiable, $this->ip))
            ->to($notifiable->email)
            ->subject('ورود به حساب کاربری');
    }

    public function toSms($notifiable)
    {
        return [
            'phone' => $notifiable->phone,
            'token' => $this->ip,
            'template' => 'login'
        ];
    }

    public function toArray(): array
    {
        return [
            'related-to' => 'events',
            'sender-image' => url('uploads/img/logo.png'),
            'sender-name' => 'متارنگ',
            'message' => 'شما با موفقیت وارد حساب کاربری خود شدید.',
        ];
    }
}
