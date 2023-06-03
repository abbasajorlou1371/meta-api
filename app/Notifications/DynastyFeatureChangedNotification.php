<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DynastyFeatureChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private string $featureId)
    {
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
        return $notifiable->hasVerifiedPhone() ? ['sms', 'broadcast'] : ['broadcast'];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        return [
            'phone' => $notifiable->phone,
            'token' => $this->featureId,
            'template' => 'dynasty-feature-changed'
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
        return [
            'related-to' => 'dynasty',
            'sender-image' => 'https://dl.qzparadise.ir/public/metarang/logo.png',
            'sender-name' => 'متارنگ',
            'message' => 'ملک بنای سلسله جایگزین شد.',
        ];
    }
}
