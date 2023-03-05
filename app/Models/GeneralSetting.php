<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'trades_email' => true,
        'trades_sms' => true,
        'transactions_email' => true,
        'transactions_sms' => true,
    ];

    public static function getChannels(User $user, $notificationType): array
    {
        $settings = self::where('user_id', $user->id)
            ->select([$notificationType . '_email', $notificationType . '_sms'])->first();
        return [
            'mail' => $settings->{$notificationType . '_email'},
            'sms' => $settings->{$notificationType . '_sms'}
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
