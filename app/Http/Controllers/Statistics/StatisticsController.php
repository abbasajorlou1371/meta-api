<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Resources\Statistics\StatisticsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StatisticsController extends Controller
{
    public $url , $types, $settings;

    public function __construct()
    {
        $this->url ='localhost:8001/api';
        $this->settings = DB::table('statistics_settings')->select('id','key')->get();
        $this->types = DB::table('statistics_types')->select('id','key')->get();
    }

    public function userFollowers(Request $request)
    {
        $followerStatistics = DB::table('statistics_types')->where('key','followers-statistics')->select('id','key')->first();
        $status = [];
        $response = Http::get($this->url . '/user-followers');
            foreach ($this->settings as $setting) {
                $fieldsStatus = adminCustomFields($followerStatistics, $setting->id);
                $status[] = [
                    'type' => $followerStatistics->key,
                    'setting' => $setting->key,
                    'status' => match ($fieldsStatus){
                        null => 0,
                        "1" => 1,
                        "0" => 0,
                    },
                ];
            }


        return response()->json([
            'top-followers' => $response->json(),
            'settings' => $status
        ]);

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
        $response = Http::post($this->url . '/current-month-users-level-one-activated', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        return $response->json();
    }
}
