<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class LogedInNotification extends KavenegarBaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private string $ip
    ) {
        //
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
        return (new MailMessage)
            ->subject('ورود به حساب کاربری')
            ->view('mail.loged-in', [
                'ip' => $this->ip,
                'user' => $notifiable,
            ]);
    }

    /**
     * Get the Kavenegar / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Kavenegar\Laravel\Message\KavenegarMessage
     */
    public function toKavenegar($notifiable)
    {
        return (new KavenegarMessage())
            ->verifyLookup('login', $this->ip);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'related-to' => 'events',
            'sender-image' => url('uploads/img/logo.png'),
            'sender-name' => 'متارنگ',
            'message' => 'شما با موفقیت وارد حساب کاربری خود شدید.',
        ];
    }
}
