<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DynastyCreatedNotification extends Notification implements ShouldQueue
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
            'token10' => $notifiable->name,
            'template' => 'dynasty-created'
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
            'sender-image' => url('uploads/img/logo.png'),
            'sender-name' => 'متارنگ',
            'message' => 'سلسله شما تاسیس شد.',
        ];
    }
}
