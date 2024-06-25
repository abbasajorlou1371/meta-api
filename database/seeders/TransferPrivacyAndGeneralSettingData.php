<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use App\Models\Privacy;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransferPrivacyAndGeneralSettingData extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Privacy::chunk(10, function ($privacies) {
            foreach ($privacies as $privacy) {
                $privacyData = [];

                $privacyFields = [
                    'nationality', 'fname', 'lname', 'name', 'birthdate', 'phone', 'email', 'address', 'about',
                    'registered_at', 'position', 'level', 'score', 'licenses', 'license_score', 'avatar', 'occupation',
                    'education', 'loved_city', 'loved_country', 'loved_language', 'prediction', 'memory', 'passions',
                    'amoozeshi_features', 'maskoni_features', 'tejari_features', 'gardeshgari_features', 'fazasabz_features',
                    'behdashti_features', 'edari_features', 'nemayeshgah_features', 'bought_golden_keys', 'used_golden_keys',
                    'recieved_golden_keys', 'bought_bronze_keys', 'used_bronze_keys', 'recieved_bronze_keys',
                    'establish_store_license', 'establish_union_license', 'establish_taxi_license',
                    'establish_amoozeshgah_license', 'reporter_license', 'cooporation_license', 'developer_license',
                    'inspection_license', 'trading_license', 'lawyer_license', 'city_council_license', 'governer_license',
                    'ostandar_license', 'level_one_judge_license', 'level_two_judge_license', 'level_three_judge_license',
                    'gate_license', 'all_licenses', 'referrals', 'irr_income', 'psc_income', 'complaint', 'warnings',
                    'commited_crimes', 'satisfaction', 'referral_profit', 'irr_transactions', 'psc_transactions',
                    'blue_transactions', 'yellow_transactions', 'red_transactions', 'sold_features', 'bought_features',
                    'sold_products', 'bought_products', 'recieved_irr_prizes', 'recieved_psc_prizes',
                    'recieved_yellow_prizes', 'recieved_blue_prizes', 'recieved_red_prizes',
                    'recieved_satisfaction_prizes', 'dynasty_members_photo', 'dynasty_members_info',
                    'recieved_dynasty_satisfaction_prizes', 'recieved_dynasty_referral_profit_prizes',
                    'recieved_dynasty_accumulated_capital_reserve_prizes', 'recieved_dynasty_data_storage_prizes',
                    'followers', 'followers_count', 'following', 'following_count', 'violations', 'breaking_laws',
                    'paid_psc_fine', 'paid_irr_fine', 'life_style', 'negative_score', 'code', 'avatar'
                ];

                foreach ($privacyFields as $field) {
                    $privacyData[$field] = $privacy->where('name', $field)->first()->display;
                }

                Setting::where('user_id', $privacy->user_id)->update([
                    'privacy' => $privacyData,
                ]);
            }
        });

        // Privacy::truncate();

        GeneralSetting::chunk(100, function ($generalSettings) {
            $settingIds = $generalSettings->pluck('user_id');
            $settings = Setting::whereIn('user_id', $settingIds)->get();

            foreach ($generalSettings as $index => $generalSetting) {
                $setting = $settings->where('user_id', $generalSetting->user_id)->first();

                if ($setting) {
                    $notifications = [
                        'announcements_sms' => $generalSetting->announcements_sms,
                        'announcements_email' => $generalSetting->announcements_email,
                        'reports_sms' => $generalSetting->reports_sms,
                        'reports_email' => $generalSetting->reports_email,
                        'login_verification_sms' => $generalSetting->login_verification_sms,
                        'login_verification_email' => $generalSetting->login_verification_email,
                        'transactions_sms' => $generalSetting->transactions_sms,
                        'transactions_email' => $generalSetting->transactions_email,
                        'trades_sms' => $generalSetting->trades_sms,
                        'trades_email' => $generalSetting->trades_email,
                    ];

                    $setting->update([
                        'notifications' => $notifications,
                    ]);
                }
            }
        });

        // GeneralSetting::truncate();
    }
}
