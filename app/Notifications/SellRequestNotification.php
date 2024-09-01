<?php

namespace App\Notifications;

use App\Mail\SellRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class SellRequestNotification extends KavenegarBaseNotification implements ShouldQueue
{
    use Queueable;

    public $feature;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($feature)
    {
        $this->feature = $feature;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return array_keys(array_filter($notifiable->getNotificationSettings('trades'), function ($key) {
            return $key;
        }));
    }

    /**
     * Send SMS Notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('درخواست فروش ملک')
            ->view('mail.sell-request', [
                'feature' => $this->feature
            ]);
    }

    /**
     * Get the Kavenegar / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return KavenegarMessage
     */
    public function toKavenegar($notifiable)
    {
        return (new KavenegarMessage())
            ->verifyLookup(
                'sell-land-metarang',
                $this->feature->properties->id
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'related-to' => 'sell-requests',
            'sender-name' => 'متارنگ',
            'sender-image' => url('uploads/img/logo.png'),
            'message' => sprintf('ملک %s با موفقیت قیمت گذاری شد.', $this->feature->properties->id)
        ];
    }
}
