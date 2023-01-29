<?php

namespace App\Http\Controllers\Api\V1\Statistics;

use App\Constants\StatisticsTypes;
use App\Helpers\GetUserCustomFields;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TradedFeaturesStatisticsController extends Controller
{
    public $tradedFeatures, $settings, $userCustomFields;
    public $url = 'localhost:8001/api';

    public function __construct()
    {
        $this->tradedFeatures = DB::table('statistics_types')
            ->where('key', '=', StatisticsTypes::FEATURESSTATISTICS)->select('id', 'key')->first();

        $this->settings = $this->settings = DB::table('statistics_settings')->select('id', 'key')->get();

        $this->userCustomFields = DB::table('user_statistics_setting')->where('statistics_type_id', '=', $this->tradedFeatures->id)->where('user_id', auth('sanctum')->user()->id)->get();

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $settings = [];
        $response = Http::post($this->url . '/traded-features', [
            'user_ids' => $request->get('user_ids'),
            'feature_ids' => $request->get('feature_ids')
        ]);
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->tradedFeatures, $setting->id);
            $settings[] = [
                'type' => $this->tradedFeatures->key,
                'setting' => $setting->key,
                'status' => match ($fieldsStatus) {
                    null => 0,
                    "1" => 1,
                    "0" => 0,
                },
            ];
        }
        $userCustomFields = GetUserCustomFields::getUserCustomFields($this->userCustomFields);
        return response()->json([
            'top-traded-features' => $response->json(),
            'admin-settings' => $settings,
            'user-custom-fields' => $userCustomFields,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currentMonthTopTradedFeatures(Request $request): JsonResponse
    {
        $settings = [];
        $response = Http::post($this->url . '/current-month-top-traded-features', [
            'user_ids' => $request->get('user_ids'),
            'feature_ids' => $request->get('feature_ids'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->tradedFeatures, $setting->id);
            $settings[] = [
                'type' => $this->tradedFeatures->key,
                'setting' => $setting->key,
                'status' => match ($fieldsStatus) {
                    null => 0,
                    "1" => 1,
                    "0" => 0,
                },
            ];
        }
        $userCustomFields = GetUserCustomFields::getUserCustomFields($this->userCustomFields);
        return response()->json([
            'top-traded-features' => $response->json(),
            'admin-settings' => $settings,
            'user-custom-fields' => $userCustomFields,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request): JsonResponse
    {
        GetUserCustomFields::updateFieldsStatus(auth('sanctum')->user()->id, $request->get('statistics_type_id'), $request->get('statistics_settings_id'));

        return response()->json([
            'message' => 'عملیات با موفقیت انجام شد'
        ]);
    }
}
