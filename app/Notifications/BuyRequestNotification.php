<?php

namespace App\Notifications;

use App\Mail\BuyRequestRecievedMail;
use App\Mail\BuyRequestSentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class BuyRequestNotification extends KavenegarBaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $data;

    public function __construct($data)
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
        return array_keys(array_filter($notifiable->getNotificationSettings('trades'), function ($key) {
            return $key;
        }));
    }

    public function toMail($notifiable)
    {
        return $this->data['type'] == 'buyer'
            ? (new BuyRequestSentMail($this->data['buyRequest']))->to($notifiable->email)
            : (new BuyRequestRecievedMail($this->data['buyRequest']))->to($notifiable->email);
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
            ->verifyLookup('buy-land-request', [
                'token' => $this->data['id'],
                'token2' => $this->data['price_psc'] == 0 ? 0 : number_format($this->data['price_psc'], 0, '.', ','),
                'token3' => $this->data['price_irr'] == 0 ? 0 : number_format($this->data['price_irr'], 0, '.', ','),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if ($this->data['type'] == 'buyer') {
            $message = sprintf(
                'مبلغ %s psc و %s از حساب شما بابت پیشنهاد خرید ملک %s برداشت شد.',
                $this->data['price_psc'],
                $this->data['price_irr'],
                $this->data['id']
            );
        } else {
            $message = sprintf('یک پیشنهاد خرید برای ملک %s دریافت شد.', $this->data['id']);
        }
        return [
            'related-to' => 'transactions',
            'sender-name' => 'متارنگ',
            'sender-image' => url('uploads/img/logo.png'),
            'message' => $message
        ];
    }
}
