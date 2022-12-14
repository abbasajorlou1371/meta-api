<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(User::cursor() as $user) {
            DB::table('privacies')->insert([
                [
                    'user_id' => $user->id,
                    'name' => 'nationality',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'fname',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'lname',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'birthdate',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'phone',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'email',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'address',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'about',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'name',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'registered_at',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'position',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'level',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'score',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'licenses',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'license_score',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'avatar',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'occupation',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'education',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'loved_city',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'loved_country',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'loved_language',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'prediction',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'memory',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'passions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'amoozeshi_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'maskoni_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'tejari_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'gardeshgari_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'fazasabz_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'behdashti_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'edari_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'nemayeshgah_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'bought_golden_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'used_golden_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_golden_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'bought_bronze_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'used_bronze_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_bronze_keys',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'establish_store_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'establish_union_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'establish_taxi_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'establish_amoozeshgah_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'reporter_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'cooporation_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'developer_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'inspection_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'trading_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'lawyer_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'city_council_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'governer_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'ostandar_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'level_one_judge_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'level_two_judge_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'level_three_judge_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'gate_license',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'all_licenses',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'referrals',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'irr_income',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'psc_income',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'complaint',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'warnings',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'commited_crimes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'satisfaction',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'referral_profit',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'irr_transactions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'psc_transactions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'blue_transactions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'yellow_transactions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'red_transactions',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'sold_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'bought_features',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'sold_products',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'bought_products',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_irr_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_psc_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_yellow_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_blue_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_red_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_satisfaction_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'dynasty_members_photo',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'dynasty_members_info',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_dynasty_satisfaction_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_dynasty_referral_profit_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_dynasty_accumulated_capital_reserve_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'recieved_dynasty_data_storage_prizes',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'followers',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'followers_count',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'following',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'following_count',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'violations',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'breaking_laws',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'paid_psc_fine',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'paid_irr_fine',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'life_style',
                ],
                [
                    'user_id' => $user->id,
                    'name' => 'negative_score',
                ],
            ]);
        }
    }
}
