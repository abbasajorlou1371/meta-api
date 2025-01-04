<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\ReferralOrderHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixReferralOrderHistories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReferralOrderHistory::chunk(100, function ($histories) {
            foreach ($histories as $history) {
                $referral = Referral::where('reference_id', $history->reference_id)
                    ->where('referrer_id', $history->referrer_id)
                    ->first();
                $history->update([
                    'referral_id' => $referral->id,
                ]);
            }
        });
    }
}
