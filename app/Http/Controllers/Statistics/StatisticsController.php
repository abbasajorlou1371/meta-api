<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StatisticsController extends Controller
{
    public string $url = 'localhost:8001/api';

    public function userFollowers()
    {
        $response = Http::post($this->url . '/user-followers');
        return $response->json();
    }

    public function currentMonthTopUserFollowers(Request $request)
    {
        $response = Http::post($this->url . '/current-month-tops', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);
        return $response->json();
    }

    public function topActiveUsers()
    {
        $response = Http::post($this->url . '/top-active-users');
        return $response->json();
    }

    public function currentMonthTopActiveUsers(Request $request)
    {
        $response = Http::post($this->url . '/current-month-top-active-users', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        return $response->json();
    }

    public function assetBuyAmount(Request $request)
    {
        $response = Http::post($this->url . '/buy-asset', [
            'asset' => $request->get('asset')
        ]);
        return $response->json();
    }


    /**
     * @param Request $request
     * @return array|mixed
     */
    public function currentMonthAssetBuyAmount(Request $request): mixed
    {
        $response = Http::post($this->url . '/current-month-top-asset-purchased', [
            'asset' => $request->get('asset'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);
        return $response->json();
    }

    public function tradedFeatures(Request $request)
    {
        $response = Http::post($this->url . '/traded-features', [
            'user_ids' => $request->get('user_ids'),
            'feature_ids' => $request->get('feature_ids')
        ]);
        return $response->json();
    }

    public function currentMonthTopTradedFeatures(Request $request)
    {
        $response = Http::post($this->url . '/current-month-top-traded-features', [
            'user_ids' => $request->get('user_ids'),
            'feature_ids' => $request->get('feature_ids'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        return $response->json();
    }

    public function topDynastyFamilyReferral()
    {
        $response = Http::get($this->url . '/top-dynasty-family-referral');
        return $response->json();
    }

    public function currentMonthTopDynastyFamilyReferral(Request $request)
    {
        $response = Http::post($this->url . '/current-month-top-dynasty-family-referral', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);
        return $response->json();
    }

    public function allUsersLevelOneActivated()
    {
        $response = Http::get($this->url . '/all-users-level-one-activated');
        return $response->json();
    }

    public function currentMonthUsersLevelOneActivated(Request $request)
    {
        $response = Http::post($this->url.'/current-month-users-level-one-activated',[
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        return $response->json();
    }
}
