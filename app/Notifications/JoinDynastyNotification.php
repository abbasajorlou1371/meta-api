<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class JoinDynastyNotification extends KavenegarBaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $data;

    public function __construct(array $data)
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
        return ['mail', 'database', 'kavenegar', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return match ($this->data['type']) {
            'requester_confirmation_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله ارسال شد')
                ->view('mail.dynasty.join-request-sent', [
                    'request' => $this->data['request']
                ]),
            'reciever_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله دریافت شد')
                ->view('mail.dynasty.join-request-received', [
                    'request' => $this->data['request']
                ]),
            'requester_accept_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله پذیرفته شد')
                ->view('mail.dynasty.join-request-accepted', [
                    'request' => $this->data['request']
                ]),
            'reciever_accept_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله پذیرفته شد')
                ->view('mail.dynasty.join-request-accepted', [
                    'request' => $this->data['request']
                ]),
            'requester_reject_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله رد شد')
                ->view('mail.dynasty.join-request-rejected', [
                    'request' => $this->data['request']
                ]),
            'reciever_reject_message' => (new MailMessage)
                ->subject('درخواست پیوستن به سلسله رد شد')
                ->view('mail.dynasty.join-request-rejected', [
                    'request' => $this->data['request']
                ]),
        };
    }

    /**
     * Get the Kavenegar / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return KavenegarMessage
     */
    public function toKavenegar($notifiable)
    {
        $message = $this->messageData();

        return (new KavenegarMessage())
            ->verifyLookup($message['template'], [
                'token' => $message['token'],
                'token10' => $message['token10'],
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
        return [
            'related-to' => 'dynasty-join-requests',
            'sender-image' => url('uploads/img/logo.png'),
            'sender-name' => 'متارنگ',
            'message' => $this->data['message']
        ];
    }

    /**
     * Prepare the data for the notification.
     *
     * @return array
     */
    private function messageData()
    {
        return match ($this->data['type']) {
            'requester_confirmation_message' => [
                'token10' => $this->data['request']->toUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-sent',
            ],
            'reciever_message' => [
                'token10' => $this->data['request']->fromUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-received',
            ],
            'requester_accept_message' => [
                'token10' => $this->data['request']->toUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-accepted',
            ],
            'reciever_accept_message' => [
                'token10' => $this->data['request']->toUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-accepted',
            ],
            'requester_reject_message' => [
                'token10' => $this->data['request']->toUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-rejected',
            ],
            'reciever_reject_message' => [
                'token10' => $this->data['request']->toUser->name,
                'token' => getRelationshipTitle($this->data['request']->relationship),
                'template' => 'dynasty-join-request-rejected',
            ],
        };
    }
}
