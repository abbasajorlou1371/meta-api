<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\sellFeature as SellFeatureMail;

class sellFeature extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $data, $trade;
    public function __construct($data, Trade $trade)
    {
        $this->data = $data;
        $this->trade = $trade;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return array_keys(array_filter($notifiable->getNotificationSettings('trades'), function ($key) {
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
        return (new SellFeatureMail($this->trade->feature))
            ->to($notifiable->email);
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSms($notifiable): array
    {
        return [
            'phone' => $notifiable->phone,
            'token' => $this->data['id'],
            'token20' => $this->data['seller'],
            'token10' => $this->data['buyer'],
            'template' => $this->data['template']
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        if ($this->trade->psc_amount > 0 && $this->trade->irr_amount > 0) {
            $message = sprintf(
                'مبلغ %s psc و %s به حساب شما بابت فروش ملک %s واریز شد.',
                $this->trade->psc_amount,
                $this->trade->irr_amount,
                $this->trade->feature->properties->id
            );
        } elseif ($this->trade->psc_amount > 0) {
            $message = sprintf(
                'مبلغ %s psc به حساب شما بابت فروش ملک %s واریز شد.',
                $this->trade->psc_amount,
                $this->trade->feature->properties->id
            );
        } elseif ($this->trade->irr_amount > 0) {
            $message = sprintf(
                'مبلغ %s ریال به حساب شما بابت فروش ملک %s واریز شد.',
                $this->trade->irr_amount,
                $this->trade->feature->properties->id
            );
        }
        return [
            'related-to' => 'transactions',
            'sender-name' => 'متارنگ',
            'sender-image' => url('uploads/img/logo.png'),
            'message' => $message
        ];
    }
}
