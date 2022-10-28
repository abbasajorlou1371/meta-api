<?php

namespace App\Services;

use App\Channels\SmsChannel;
use App\Models\User;

class NotificationService {
    public static function getChannels(User $notifiable, $notificationType)
    {
        switch($notificationType)
        {
            case 'announcements':
                if(
                    $notifiable->generalSettings->announcements_sms
                    && $notifiable->generalSettings->announcements_email
                )
                {
                    return ['mail', SmsChannel::class];
                }

                if($notifiable->generalSettings->announcements_sms) {
                    return [SmsChannel::class];
                }

                if($notifiable->generalSettings->announcements_email) {
                    return ['mail'];
                }
                break;
            case 'login':
                if(
                    $notifiable->generalSettings->login_verification_sms
                    && $notifiable->generalSettings->login_verification_email
                )
                {
                    return ['mail', SmsChannel::class];
                }

                if($notifiable->generalSettings->login_verification_sms) {
                    return [SmsChannel::class];
                }

                if($notifiable->generalSettings->login_verification_email) {
                    return ['mail'];
                }
                break;
            case 'reports':
                if(
                    $notifiable->generalSettings->reports_sms
                    && $notifiable->generalSettings->reports_email
                )
                {
                    return ['mail', SmsChannel::class];
                }

                if($notifiable->generalSettings->reports_sms) {
                    return [SmsChannel::class];
                }

                if($notifiable->generalSettings->reports_email) {
                    return ['mail'];
                }
                break;
            case 'transactions':
                if(
                    $notifiable->generalSettings->transactions_sms
                    && $notifiable->generalSettings->transactions_email
                )
                {
                    return ['mail', SmsChannel::class];
                }

                if($notifiable->generalSettings->transactions_sms) {
                    return [SmsChannel::class];
                }

                if($notifiable->generalSettings->transactions_email) {
                    return ['mail'];
                }
                break;
            case 'trades':
                if(
                    $notifiable->generalSettings->trades_sms
                    && $notifiable->generalSettings->trades_email
                )
                {
                    return ['mail', SmsChannel::class];
                }

                if($notifiable->generalSettings->trades_sms) {
                    return [SmsChannel::class];
                }

                if($notifiable->generalSettings->trades_email) {
                    return ['mail'];
                }
                break;
        }
    }
}
