<?php

namespace App\Http\Controllers\Statistics;

use App\Constants\StatisticsTypes;
use App\Helpers\GetUserCustomFields;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UserActivityStatistics extends Controller
{
    public $userActivityStatistic, $settings, $userCustomFields;
    public $url = 'localhost:8001/api';

    public function __construct()
    {
        $this->userActivityStatistic = DB::table('statistics_types')
            ->where('key', '=', StatisticsTypes::ACTIVITYSTATISTICS)->select('id', 'key')->first();

        $this->settings = $this->settings = DB::table('statistics_settings')->select('id', 'key')->get();

        $this->userCustomFields = DB::table('user_statistics_setting')->where('statistics_type_id', '=', $this->userActivityStatistic->id)->where('user_id', auth('sanctum')->user()->id)->get();

    }


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = [];
        $response = Http::get($this->url . '/top-active-users');
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->userActivityStatistic, $setting->id);
            $settings[] = [
                'type' => $this->userActivityStatistic->key,
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
            'top-followers' => $response->json(),
            'admin-settings' => $settings,
            'user-custom-fields' => $userCustomFields,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currentMonthTopActiveUsers(Request $request): JsonResponse
    {
        $settings = [];
        $response = Http::post($this->url . '/current-month-top-active-users', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->userActivityStatistic, $setting->id);
            $settings[] = [
                'type' => $this->userActivityStatistic->key,
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
            'current-month-top-followers' => $response->json(),
            'admin-settings' => $settings,
            'user-custom-fields' => $userCustomFields
        ]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        GetUserCustomFields::updateFieldsStatus(auth('sanctum'), $request->get('statistics_type_id'), $request->get('statistics_settings_id'));

        return response()->json([
            'message' => 'عملیات با موفقیت انجام شد'
        ]);
    }
}
