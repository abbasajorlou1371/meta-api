<?php

namespace App\Helpers;

use App\Models\BuyFeatureRequest;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Http\Request;

class BuyFeatureRequestHelper
{

    public static function checkErrors(User $buyer, Request $request, Feature $feature)
    {
        $user_requested = BuyFeatureRequest::where('buyer_id', $buyer->id)
            ->where('feature_id', $feature->id)
            ->where('status', 0)
            ->first();

        $totalPrice = totalPrice($feature, 'buyer', fee($feature));

        if ($request->price_irr == 0 && $request->price_psc == 0) {
            return 'قیمت قیمت پیشنهادی خود را یا به تومان یا به psc مشخص کنید';
        }

        if (! empty($user_requested) ) {

            return 'شما قبلا درخواست خرید خود را برای این ملک ارسال کرده اید';
        }
        if (
            $totalPrice['psc'] > $buyer->assets->psc
            || $request->price_psc > $buyer->assets->psc
        ) {

            return 'موجودیpsc  شما برای ارسال درخواست خرید کافی نمی باشد';
        }

        if (
            $totalPrice['irr'] > $buyer->assets->irr
            || $request->price_irr > $buyer->assets->irr
        ) {

            return 'موجودی ریال شما برای ارسال درخواست خرید کافی نمی باشد';
        }
        return null;
    }
}
