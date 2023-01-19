<?php

namespace App\Http\Controllers\Statistics;

use App\Constants\StatisticsTypes;
use App\Helpers\GetUserCustomFields;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FollowersStatisticsController extends Controller
{
    public $followersStatistic, $settings, $userCustomFields;
    public $url = 'localhost:8001/api';

    public function __construct()
    {
        $this->followersStatistic = DB::table('statistics_types')
            ->where('key', '=', StatisticsTypes::FOLLOWERSSTATISTICS)->select('id', 'key')->first();

        $this->settings = $this->settings = DB::table('statistics_settings')->select('id', 'key')->get();

        $this->userCustomFields = DB::table('user_statistics_setting')->where('statistics_type_id', '=', $this->followersStatistic->id)->where('user_id', auth('sanctum')->user()->id)->get();

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = [];
        $response = Http::get($this->url . '/user-followers');
        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->followersStatistic, $setting->id);
            $settings[] = [
                'type' => $this->followersStatistic->key,
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
    public function currentMonthTopFollowers(Request $request): JsonResponse
    {
        $settings = [];
        $response = Http::post($this->url . '/current-month-tops', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);

        foreach ($this->settings as $setting) {
            $fieldsStatus = adminCustomFields($this->followersStatistic, $setting->id);
            $settings[] = [
                'type' => $this->followersStatistic->key,
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


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request): JsonResponse
    {
        GetUserCustomFields::updateFieldsStatus(auth('sanctum'), $request->get('statistics_type_id'), $request->get('statistics_settings_id'));

        return response()->json([
            'message' => 'عملیات با موفقیت انجام شد'
        ]);
    }



//    public function getUserFollowersCustomFields(): array
//    {
//        $userFields = [];
//        foreach ($this->userCustomFields as $field) {
//            if ($field->status == 1) {
//
//                $setting = DB::table('statistics_settings')->where('id', '=', $field->statistics_settings_id)->select('key')->first();
//                if ($setting->key == StatisticsSettings::CITIZENCODE) {
//                    $userCode = DB::table('users')->where('id', '=', $field->user_id)->select('code')->first();
//                    $userFields[] = [
//                        'user-code' => $userCode
//                    ];
//                } elseif ($setting->key == StatisticsSettings::CITIZENIMAGE) {
//                    $citizenImage = DB::table('images')->where('imageable_id', '=', $field->user_id)
//                        ->where('imageable_type', '=', 'App\Models\User')->select('url')->first();
//                    $userFields[] = [
//                        'citizen-image' => $citizenImage
//                    ];
//                } elseif ($setting->key == StatisticsSettings::DYNASTYMEMBERSLIST) {
//                    $userFamilyId = DB::table('family_members')->where('user_id', '=', $field->user_id)->select('family_id')->first();
//                    $memberIds = DB::table('family_members')->where('family_id', '=', $userFamilyId)->select('user_id')->get();
//                    $userFamilyMembers = DB::table('users')->whereIn('id', $memberIds)->select('name', 'code')->get();
//                    $userFields[] = [
//                        'user-dynasty-members' => $userFamilyMembers
//                    ];
//                } elseif ($setting->key == StatisticsSettings::FOLLOWERSCOUNT) {
//                    $followersCount = DB::table('follows')->where('following_id', '=', $field->user_id)->count();
//                    $userFields[] = [
//                        'user-followers-count' => $followersCount,
//                    ];
//                } elseif ($setting->key == StatisticsSettings::FOLLOWINGCOUNT) {
//                    $followersCount = DB::table('follows')->where('follower_id', '=', $field->user_id)->count();
//                    $userFields[] = [
//                        'user-following-count' => $followersCount,
//                    ];
//                } elseif ($setting->key == StatisticsSettings::LASTNAME) {
//                    $userLastName = DB::table('kycs')->where('id', '=', $field->user_id)->select('lname')->first();
//                    $userFields[] = [
//                        'user-lastname' => $userLastName
//                    ];
//                } elseif ($setting->key == StatisticsSettings::LASTSTATUSDATE) {
//                    $userLastSeen = DB::table('users')->where('id', '=', $field->user_id)->select('last_seen')->first();
//                    $userFields[] = [
//                        'user-lastname' => Jalalian::forge($userLastSeen)->format('Y/m/d')
//                    ];
//                } elseif ($setting->key == StatisticsSettings::LASTSTATUSHOUR) {
//                    $userLastSeen = DB::table('users')->where('id', '=', $field->user_id)->select('last_seen')->first();
//                    $userFields[] = [
//                        'user-lastname' => Jalalian::forge($userLastSeen)->format('H:i:s')
//                    ];
//                } elseif ($setting->key == StatisticsSettings::LEVEL) {
//                    $levelId = DB::table('user_level')->where('user_id', $field->user_id)->select('level_id')->first();
//                    $userLevel = DB::table('levels')->where('id', $levelId)->select('name', 'slug')->first();
//                    $userFields[] = [
//                        'user-level' => $userLevel->name,
//                        'user-level-slug' => $userLevel->slug,
//                    ];
//                } elseif ($setting->key == StatisticsSettings::TRYCHART) {
//
//                }
//            } else {
//                return $userFields;
//            }
//        }
//        return $userFields;
//    }

}
