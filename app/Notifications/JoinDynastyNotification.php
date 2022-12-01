<?php

namespace App\Notifications;

use App\Mail\Dynasty\RecieverConfirmationMail;
use App\Mail\Dynasty\SenderConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JoinDynastyNotification extends Notification implements ShouldQueue
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
        $this->afterCommit();
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
                return (new SenderConfirmationMail($this->data['title'], $this->data['message']))
                    ->from('rgb@gmail.com', 'متارنگ')
                    ->to($notifiable->email);
                break;
            case 'reciever_message':
                return (new RecieverConfirmationMail($this->data['title'], $this->data['message']))
                    ->from('rgb@gmail.com')
                    ->to($notifiable->email);
                break;
            case 'requester_accept_message':
                return (new SenderConfirmationMail($this->data['title'], $this->data['message']))
                    ->from('rgb@gmail.com', 'متارنگ')
                    ->to($notifiable->email);
                break;
            case 'reciever_accept_message':
                return (new RecieverConfirmationMail($this->data['title'], $this->data['message']))
                    ->from('rgb@gmail.com')
                    ->to($notifiable->email);
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
        switch ($this->data['type']) {
            case 'requester_confirmation_message':
                return [
                    'درخواست پیوستن به سلسله شما ارسال گردید.'
                ];
                break;
            case 'reciever_message':
                return [
                    'درخواستی جهت پیوستن به سلسله دریافت شد.'
                ];
            case 'requester_accept_message':
                return [
                    'درخواست پیوستن به سلسله شما توسط کاربر مورد نظر پذیرفته شد.'
                ];
                break;
            case 'reciever_accept_message':
                return [
                    'شما درخواست پیوستن به سلسله را پذیرفته و به سلسله پیوستید.'
                ];
            case 'requester_reject_message':
                return [
                    $this->data['message']
                ];
            default:
                return [];
        }
    }
}
