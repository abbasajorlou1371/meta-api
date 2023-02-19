<?php

namespace App\Services;

use App\Models\GeneralSetting;
use App\Models\User;

class NotificationService
{
    public static function getChannels(User $notifiable, $notificationType): array
    {
        $settings = GeneralSetting::where('user_id', $notifiable->id)
            ->select([$notificationType . '_email', $notificationType . '_smal'])->first();
        return [
            'mail' => $settings->{$notificationType} . '_email',
            'sms' => $settings->{$notificationType} . '_sms'
        ];
    }
}
