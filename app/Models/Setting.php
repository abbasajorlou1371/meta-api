<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'automatic_logout' => 'integer',
        'privacy' => 'array',
        'notifications' => 'array',
    ];

    protected $attributes = [
        'automatic_logout' => 60,
        'notifications' => [
            'trades_email' => true,
            'trades_sms' => true,
            'transactions_email' => true,
            'transactions_sms' => true,
            'login_verification_email' => true,
            'login_verification_sms' => true,
            'reports_email' => true,
            'reports_sms' => true,
            'announcements_email' => true,
            'announcements_sms' => true,
        ],
        'privacy' => [
            'nationality' => true,
            'fname' => true,
            'birthdate' => true,
            'phone' => false,
            'email' => false,
            'address' => false,
            'about' => true,
            'name' => true,
            'registered_at' => true,
            'position' => true,
            'level' => true,
            'score' => true,
            'licenses' => true,
            'license_score' => true,
            'avatar' => true,
            'occupation' => true,
            'education' => true,
            'loved_city' => true,
            'loved_country' => true,
            'loved_language' => true,
            'prediction' => true,
            'memory' => true,
            'passions' => true,
            'amoozeshi_features' => true,
            'maskoni_features' => true,
            'tejari_features' => true,
            'gardeshgari_features' => true,
            'fazasabz_features' => true,
            'behdashti_features' => true,
            'edari_features' => true,
            'nemayeshgah_features' => true,
            'bought_golden_keys' => true,
            'used_golden_keys' => true,
            'recieved_golden_keys' => true,
            'bought_bronze_keys' => true,
            'used_bronze_keys' => true,
            'recieved_bronze_keys' => true,
            'establish_store_license' => true,
            'establish_union_license' => true,
            'establish_taxi_license' => true,
            'establish_amoozeshgah_license' => true,
            'reporter_license' => true,
            'cooporation_license' => true,
            'developer_license' => true,
            'inspection_license' => true,
            'trading_license' => true,
            'lawyer_license' => true,
            'city_council_license' => true,
            'governer_license' => true,
            'ostandar_license' => true,
            'level_one_judge_license' => true,
            'level_two_judge_license' => true,
            'level_three_judge_license' => true,
            'gate_license' => true,
            'all_licenses' => true,
            'referrals' => true,
            'irr_income' => true,
            'psc_income' => true,
            'complaint' => true,
            'warnings' => true,
            'commited_crimes' => true,
            'satisfaction' => true,
            'referral_profit' => true,
            'irr_transactions' => true,
            'psc_transactions' => true,
            'blue_transactions' => true,
            'yellow_transactions' => true,
            'red_transactions' => true,
            'sold_features' => true,
            'bought_features' => true,
            'sold_products' => true,
            'bought_products' => true,
            'recieved_irr_prizes' => true,
            'recieved_psc_prizes' => true,
            'recieved_yellow_prizes' => true,
            'recieved_blue_prizes' => true,
            'recieved_red_prizes' => true,
            'recieved_satisfaction_prizes' => true,
            'dynasty_members_photo' => true,
            'dynasty_members_info' => true,
            'recieved_dynasty_satisfaction_prizes' => true,
            'recieved_dynasty_referral_profit_prizes' => true,
            'recieved_dynasty_accumulated_capital_reserve_prizes' => true,
            'recieved_dynasty_data_storage_prizes' => true,
            'followers' => true,
            'followers_count' => true,
            'following' => true,
            'following_count' => true,
            'violations' => true,
            'breaking_laws' => true,
            'paid_psc_fine' => true,
            'paid_irr_fine' => true,
            'life_style' => true,
            'negative_score' => true,
            'code' => true,
        ]
    ];

    public static function getChannels(User $user, $notificationType): array
    {
        $settings = self::where('user_id', $user->id)->select('id', 'user_id', 'notifications')->first();

        return [
            'mail' => $settings->notifications[$notificationType . '_email'],
            'sms' => $user->hasVerifiedPhone() ? $settings->notifications[$notificationType . '_sms'] : 0,
            'broadcast' => 1
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
