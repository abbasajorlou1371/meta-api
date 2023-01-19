<?php

namespace App\Http\Controllers\Statistics;

use App\Constants\StatisticsTypes;
use App\Helpers\GetUserCustomFields;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FamilyMembersStatisticsController extends Controller
{
    public $referralStatistics, $settings, $userCustomFields;
    public $url = 'localhost:8001/api';

    public function __construct()
    {
        $this->referralStatistics = DB::table('statistics_types')
            ->where('key', '=', StatisticsTypes::REFERRALSTATISTICS)->select('id', 'key')->first();

        $this->settings = $this->settings = DB::table('statistics_settings')->select('id', 'key')->get();

        $this->userCustomFields = DB::table('user_statistics_setting')->where('statistics_type_id', '=', $this->referralStatistics->id)->where('user_id', auth('sanctum')->user()->id)->get();

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = [];
        $response = Http::get($this->url . '/top-dynasty-family-referral');
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->referralStatistics, $setting->id);
            $settings[] = [
                'type' => $this->referralStatistics->key,
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
            'family-members' => $response->json(),
            'admin-settings' => $settings,
            'user-custom-fields' => $userCustomFields,
        ]);
    }

    public function currentMonthTopDynastyFamilyReferral(Request $request)
    {
        $settings = [];
        $response = Http::post($this->url . '/current-month-top-dynasty-family-referral', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->referralStatistics, $setting->id);
            $settings[] = [
                'type' => $this->referralStatistics->key,
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
            'family-members' => $response->json(),
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
