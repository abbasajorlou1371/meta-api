<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Variable;

class ReferralService
{
    /**
     * Handle referral when an order is placed.
     *
     * @param User $user
     * @param Order $order
     * @return void
     */
    public static function referral(User $user, Order $order)
    {
        $user->load('referred');

        if ($user->referred) {
            // If the asset is 'irr', do not proceed with referral
            if ($order->asset == 'irr') {
                return;
            }

            $psc_price = Variable::getRate('psc');
            $referred = $user->referred;

            // Calculate the total amount referred by the referred user
            $referred_amount = $referred->referalOrders()->sum('amount') * $psc_price ?? 0;

            // Calculate the referral amount for the referer based on the order asset
            if (in_array($order->asset, ['blue', 'red', 'yellow'])) {
                $referrer_amount = (($order->amount * Variable::getRate($order->asset)) / $psc_price) * 0.5;
            } else {
                $referrer_amount = $order->amount * 0.5;
            }

            $referralLimit = $referred->variables;

            // Check if the referred user has reached the referral profit limit
            if ($referred_amount >= $referralLimit->referral_profit) {
                return;
            }

            // Increment the referer's 'psc' asset with the referral amount
            $referred->wallet->increment('psc', $referrer_amount);

            // Create a new referal orders entry
            $referred->referralOrders()->create([
                'referral_id' => $user->id,
                'amount' => $referrer_amount,
            ]);
        }
    }
}
