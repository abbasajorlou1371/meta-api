<?php

namespace App\Services;

use App\Channels\SmsChannel;
use App\Models\User;

class NotificationService
{
    public static function getChannels(User $notifiable, $notificationType): array
    {
        switch ($notificationType) {
            case 'announcements':
                if (
                    $notifiable->generalSettings->announcements_sms
                    && $notifiable->generalSettings->announcements_email
                ) {
                    return ['mail', SmsChannel::class];
                }elseif ($notifiable->generalSettings->announcements_sms) {
                    return [SmsChannel::class];
                }elseif ($notifiable->generalSettings->announcements_email) {
                    return ['mail'];
                } else{
                    return [];
                }
                break;
            case 'login':
                if (
                    $notifiable->generalSettings->login_verification_sms
                    && $notifiable->generalSettings->login_verification_email
                ) {
                    return ['mail', SmsChannel::class];
                }elseif ($notifiable->generalSettings->login_verification_sms) {
                    return [SmsChannel::class];
                }elseif ($notifiable->generalSettings->login_verification_email) {
                    return ['mail'];
                }else{
                    return [];
                }
                break;
            case 'reports':
                if (
                    $notifiable->generalSettings->reports_sms
                    && $notifiable->generalSettings->reports_email
                ) {
                    return ['mail', SmsChannel::class];
                }elseif ($notifiable->generalSettings->reports_sms) {
                    return [SmsChannel::class];
                }elseif ($notifiable->generalSettings->reports_email) {
                    return ['mail'];
                }else{
                    return [];
                }
                break;
            case 'transactions':
                if (
                    $notifiable->generalSettings->transactions_sms
                    && $notifiable->generalSettings->transactions_email
                ) {
                    return ['mail', SmsChannel::class, 'database'];
                }elseif ($notifiable->generalSettings->transactions_sms) {
                    return [SmsChannel::class, 'database'];
                }elseif ($notifiable->generalSettings->transactions_email) {
                    return ['mail', 'database'];
                }else{
                    return [];
                }
                break;
            case 'trades':
                if (
                    $notifiable->generalSettings->trades_sms
                    && $notifiable->generalSettings->trades_email
                ) {
                    return ['mail', SmsChannel::class, 'database'];
                }elseif ($notifiable->generalSettings->trades_sms) {
                    return [SmsChannel::class, 'database'];
                }elseif ($notifiable->generalSettings->trades_email) {
                    return ['mail', 'database'];
                }else{
                    return [];
                }
                break;
            default:
                return [];
        }
    }
}
