<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Variable;

class ReferalService
{
    public static function referal(User $user, Order $order)
    {
        if ($user->has_reference()) {
            if($order->asset == 'irr') return;
            $psc_price = Variable::getRate('psc');
            $reference = $user->reference;
            $reference_amount = $reference->referalOrderHistories->sum('amount') * $psc_price ?? 0;

            if(in_array($order->asset, ['blue', 'red', 'yellow'])) {
                $referer_amount = (($order->amount * Variable::getRate($order->asset)) / $psc_price) * 0.5;
            } else {
                $referer_amount = $order->amount * 0.5;
            }

            $referalLimit = $reference->variables;
            if ($reference_amount >= $referalLimit->referral_profit) return;
            $reference->assets->increment('psc', $referer_amount);
            $reference->referalOrderHistories()->create([
                'referer_id' => $user->id,
                'amount' => $referer_amount,
            ]);
        }
    }
}
