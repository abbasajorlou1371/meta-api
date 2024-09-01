<?php

namespace App\Notifications;

use App\Mail\EmailOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class GetOtpNotification extends KavenegarBaseNotification implements ShouldQueue
{
    use Queueable;

    private $code, $phone, $type, $email;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $type = 'kavenegar', $email = null, $phone = null,)
    {
        $this->code = $code;
        $this->phone = $phone;
        $this->type = $type;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return $this->type === 'kavenegar' ? ['kavenegar'] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new EmailOtp($notifiable, $this->code))
            ->to($this->email)
            ->from('rgb-robot@irpsc.com');
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
            ->verifyLookup('verify', [
                'token' => $this->code,
                'template' => 'verify'
            ]);
    }
}
