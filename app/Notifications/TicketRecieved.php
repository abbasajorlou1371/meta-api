<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketRecieved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(private Ticket $ticket)
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
        return ['database', 'broadcast'];
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
            'related-to' => 'tickets',
            'sender-image' => $this->ticket->sender->profilePhotos->last()->url ?? url('uploads/img/logo.png'),
            'sender-name' => $this->ticket->sender->name,
            'message' => 'تیکتی از طرف ' . $this->ticket->sender->name . 'دریافت شده است',
        ];
    }
}
