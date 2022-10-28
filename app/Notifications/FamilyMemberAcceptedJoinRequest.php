<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Morilog\Jalali\Jalalian;

class FamilyMemberAcceptedJoinRequest extends Notification
{
    use Queueable;

    public $joinRequest, $fromUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($joinRequest,$fromUser)
    {
        $this->joinRequest = $joinRequest;
        $this->fromUser = $fromUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'from_user' => $this->fromUser->name,
            'to_user' => $this->joinRequest->to_user,
            'relation' => $this->joinRequest->relation,
            'accepted_by' => $this->joinRequest->to_user,
            'accepted_data' => Jalalian::forge($this->joinRequest->created_at)->format('Y/m/d'),
        ];
    }

}
