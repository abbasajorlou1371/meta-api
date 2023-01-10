<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kavenegar;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {

        $data = $notification->toSms($notifiable);

        try {
            $receptor = $data['phone'];
            $token = $data['token'] ?? '';
            $token2 = $data['token2'] ?? '';
            $token3 = $data['token3'] ?? '';
            $token10 = $data['token10'] ?? '';
            $token20 = $data['token20'] ?? '';
            $template = $data['template'];

            return Kavenegar::VerifyLookup(
                $receptor,
                $token,
                $token2,
                $token3,
                $template,
                $type = null,
                $token10,
                $token20,
            );
        } catch (\Kavenegar\Exceptions\ApiException $e) {
            return $e->errorMessage();
        } catch (\Kavenegar\Exceptions\HttpException $e) {
            return $e->errorMessage();
        }
    }
}
