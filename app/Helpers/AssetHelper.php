<?php

namespace App\Helpers;

use App\Models\BuyFeatureRequest;
use App\Models\Comission;
use App\Models\Feature;
use App\Models\LockedAsset;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Trade;
use App\Events\FeatureTraded;

class AssetHelper
{

    public static function checkColorBalance(User $user, Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Tejari:
                return $user->assets->red < $feature->properties->stability;
                break;
            case FeatureIndicators::Maskoni:
                return $user->assets->yellow < $feature->properties->stability;
                break;
            case FeatureIndicators::Amozeshi:
                return $user->assets->blue < $feature->properties->stability;
                break;
        }
    }

    public static function getAssetColor(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Amozeshi:
                return 'blue';
                break;
            case FeatureIndicators::Tejari:
                return 'red';
                break;
            case FeatureIndicators::Maskoni:
                return 'yellow';
                break;
        }
    }

    public static function lockAsset(BuyFeatureRequest $buyFeatureRequest, Request $request)
    {
        $buyer = $buyFeatureRequest->buyer;
        $psc_amount = $request->price_psc + ($request->price_psc * config('rgb.fee'));
        $irr_amount = $request->price_irr + ($request->price_irr * config('rgb.fee'));

        $buyer->assets->decrement('psc', $psc_amount);
        $buyer->assets->decrement('irr', $irr_amount);

        LockedAsset::create([
            'user_id' => $buyer->id,
            'buy_feature_request_id' => $buyFeatureRequest->id,
            'feature_id' => $buyFeatureRequest->feature->id,
            'psc' => $psc_amount,
            'irr' => $irr_amount
        ]);

    }

    public static function releaseAsset(BuyFeatureRequest $buyFeatureRequest, $rejectOrCancel = false)
    {
        $psc_amount = $buyFeatureRequest->lockedAsset->psc;
        $irr_amount = $buyFeatureRequest->lockedAsset->irr;

        if ($rejectOrCancel) {
            $buyFeatureRequest->buyer->assets->increment('psc', $psc_amount);
            $buyFeatureRequest->buyer->assets->increment('irr', $irr_amount);
            $buyFeatureRequest->lockedAsset->delete();
        } else {
            $psc_total_fee = $psc_amount * config('rgb.fee') * 2;
            $irr_total_fee = $irr_amount * config('rgb.fee') * 2;

            $rgb = User::firstWhere('code', 'hm-20000');

            $rgb->assets->increment('psc', $psc_total_fee);
            $rgb->assets->increment('irr', $irr_total_fee);

            chargeBuyer($buyFeatureRequest->buyer, $buyFeatureRequest->feature);
            addSeller($buyFeatureRequest->seller, $buyFeatureRequest->feature);

            $trade = Trade::create([
                'feature_id' => $buyFeatureRequest->feature->id,
                'buyer_id' => $buyFeatureRequest->buyer->id,
                'seller_id' => $buyFeatureRequest->seller->id,
                'irr_amount' => $buyFeatureRequest->price_irr,
                'psc_amount' => $buyFeatureRequest->price_psc,
                'date' => now()
            ]);

            Comission::create([
                'trade_id' => $trade->id,
                'psc' => $psc_total_fee,
                'irr' => $irr_total_fee,
            ]);
            event(new FeatureTraded($trade));
            self::cancelOthereRequests($buyFeatureRequest);
        }
    }
    private static function cancelOthereRequests(BuyFeatureRequest $buyFeatureRequest)
    {
        $feature = $buyFeatureRequest->feature;

        foreach ($feature->buyRequests as $buyRequest) {
            if ($buyRequest->id == $buyFeatureRequest->id) continue;
            $buyRequest->update(['status' => -1]);
            $price_psc = $buyRequest->lockedAssets->psc;
            $price_irr = $buyRequest->lockedAssets->irr;

            $buyRequest->buyer->assets->increment('psc', $price_psc);
            $buyRequest->buyer->assets->increment('irr', $price_irr);
            $buyFeatureRequest->lockedAsset->delete();
        }
    }

    public static function checkBalance(User $buyer, Feature $feature)
    {
        $totalPrice = totalPrice($feature, 'buyer', fee($feature));

        if(! iszero($feature->properties->price_psc)
           && ! iszero($feature->properties->price_irr)
        )
        {
            if( $buyer->assets->psc < $totalPrice['psc'] )
            {
                return ['error' => 'موجودی psc شما کافی نمی باشد'];
            }

            if( $buyer->assets->irr < $totalPrice['irr'])
            {
                return ['error' => 'موجودی ریال شما کافی نمی باشد'];
            }
        }

        if( ! iszero($feature->properties->price_psc) ) {
            if( $buyer->assets->psc < $totalPrice['psc'] )
            {
                return ['error' => 'موجودی psc شما کافی نمی باشد'];
            }
        }

        if(! iszero($feature->properties->price_irr)) {
            if( $buyer->assets->irr < $totalPrice['irr'] )
            {
                return ['error' => 'موجودی ریال شما کافی نمی باشد'];
            }
        }

        return null;
    }
}
