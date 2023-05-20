<?php

namespace App\Http\Controllers\Api\V1\Feature;

use App\Events\FeatureStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellFeatureRequestValidate;
use App\Http\Resources\SellRequestResource;
use App\Models\Feature;
use App\Models\SellFeatureRequest;
use App\Models\SystemVariable;
use App\Models\Variable;
use App\Notifications\SellRequestNotification;

class SellRequestsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['account.security', 'verified'])->except(['index']);
    }
    /**
     * Display a listing of the Feature sell requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SellRequestResource::collection(request()->user()->sellRequests);
    }

    /**
     * Create a new sell request.
     *
     * @param SellFeatureRequestValidate $request
     * @param Feature $feature
     * @return \Illuminate\Http\Response
     */
    public function store(SellFeatureRequestValidate $request, Feature $feature)
    {
        // Get the public and under 18 pricing limits from system variables or use default values
        $publicPricingLimit = SystemVariable::getByKey('public_pricing_limit') ?? 80;
        $under18PricingLimit = SystemVariable::getByKey('under_18_pricing_limit') ?? 110;

        // Get the requested prices from the request
        $requestedPrice_psc = $request->price_psc;
        $requestedPrice_irr = $request->price_irr;

        // Check if minimum_price_percentage is provided in the request
        if ($request->has('minimum_price_percentage')) {
            // Check if the user is under 18 and the minimum_price_percentage is less than the under 18 pricing limit
            if ($request->user()->isUnderEighteen() && $request->minimum_price_percentage < $under18PricingLimit) {
                abort(403, sprintf("شما مجاز به فروش زمین خود به کمتر از %s درصد قیمت خرید ملک نمی باشید", $under18PricingLimit));
            }
            // Check if the minimum_price_percentage is less than the public pricing limit
            elseif ($request->minimum_price_percentage < $publicPricingLimit) {
                abort(403, sprintf("شما مجاز به فروش زمین خود به کمتر از %s درصد قیمت خرید ملک نمی باشید", $publicPricingLimit));
            }

            // Calculate the total price based on stability, color rate, and minimum_price_percentage
            $totalPrice = $feature->properties->stability * Variable::getRate($feature->getColor()) * $request->minimum_price_percentage / 100;
            // Calculate the requested prices in PSC and IRR based on the total price
            $requestedPrice_psc = $totalPrice / Variable::getRate('psc') * 0.5;
            $requestedPrice_irr = $totalPrice * 0.5;
            $pricing_percentage = $request->minimum_price_percentage;
        } else {
            // Calculate the total requested price in PSC and IRR
            $totalRequested_price = $request->price_psc * Variable::getRate('psc') + $request->price_irr;
            // Calculate the total traded price based on stability and color rate
            $totalTradedPrice = $feature->properties->stability * Variable::getRate($feature->getColor());
            // Calculate the pricing percentage based on the total requested price and total traded price
            $pricing_percentage = intval($totalRequested_price / $totalTradedPrice * 100);

            // Check if the user is under 18 and the pricing percentage is less than the under 18 pricing limit
            if ($request->user()->isUnderEighteen() && $pricing_percentage < $under18PricingLimit) {
                abort(403, sprintf("شما مجاز به فروش زمین خود به کمتر از %s درصد قیمت خرید ملک نمی باشید", $under18PricingLimit));
            }
            // Check if the pricing percentage is less than the public pricing limit
            elseif ($pricing_percentage < $publicPricingLimit) {
                abort(403, sprintf("شما مجاز به فروش زمین خود به کمتر از %s درصد قیمت خرید ملک نمی باشید", $publicPricingLimit));
            }
        }

        // Create a sell request with the seller ID, feature ID, requested prices, and pricing percentage
        $sellRequest = SellFeatureRequest::create([
            'seller_id' => $feature->owner->id,
            'feature_id' => $feature->id,
            'price_psc' => $requestedPrice_psc,
            'price_irr' => $requestedPrice_irr,
            'limit'     => $pricing_percentage,
        ]);

        // Update the feature properties with the new RGB, requested prices, and pricing percentage
        $feature->properties->update([
            'rgb' => $feature->changeStatusToSoldAndPriced(),
            'price_psc' => $sellRequest->price_psc,
            'price_irr' => $sellRequest->price_irr,
            'minimum_price_percentage' => $pricing_percentage
        ]);

        // Broadcast an event to notify that the feature status has changed
        broadcast(new FeatureStatusChanged([
            'id'  => $feature->id,
            'rgb' => $feature->changeStatusToSoldAndPriced(),
        ]));

        // Notify the user about the sell request
        $request->user()->notify(new SellRequestNotification($feature));

        // Return the created sell request as a resource
        return new SellRequestResource($sellRequest);
    }


    /**
     * Delete a sell request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellFeatureRequest $sellRequest)
    {
        $feature = $sellRequest->feature;

        // Update the feature properties with the new RGB
        $feature->properties->update([
            'rgb' => $feature->changeStatusToSoldAndNotPriced()
        ]);

        // Delete the sell request
        $sellRequest->delete();

        // Broadcast an event to notify that the feature status has changed
        broadcast(new FeatureStatusChanged([
            'id'  => $feature->id,
            'rgb' => $feature->changeStatusToSoldAndNotPriced()
        ]));

        return response()->noContent(200);
    }
}
