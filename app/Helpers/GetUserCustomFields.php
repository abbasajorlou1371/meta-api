<?php

namespace App\Helpers;

use App\Constants\StatisticsSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class GetUserCustomFields
{
    public static function getUserCustomFields($userCustomFields): array
    {
        $userFields = [];
        foreach ($userCustomFields as $field) {
            if ($field->status == 1) {

                $setting = DB::table('statistics_settings')->where('id', '=', $field->statistics_settings_id)->select('key')->first();
                if ($setting->key == StatisticsSettings::CITIZENCODE) {
                    $userCode = DB::table('users')->where('id', '=', $field->user_id)->select('code')->first();
                    $userFields[] = [
                        'user-code' => $userCode
                    ];
                } elseif ($setting->key == StatisticsSettings::CITIZENIMAGE) {
                    $citizenImage = DB::table('images')->where('imageable_id', '=', $field->user_id)
                        ->where('imageable_type', '=', 'App\Models\User')->select('url')->first();
                    $userFields[] = [
                        'citizen-image' => $citizenImage
                    ];
                } elseif ($setting->key == StatisticsSettings::DYNASTYMEMBERSLIST) {
                    $userFamilyId = DB::table('family_members')->where('user_id', '=', $field->user_id)->select('family_id')->first();
                    $memberIds = DB::table('family_members')->where('family_id', '=', $userFamilyId)->select('user_id')->get();
                    $userFamilyMembers = DB::table('users')->whereIn('id', $memberIds)->select('name', 'code')->get();
                    $userFields[] = [
                        'user-dynasty-members' => $userFamilyMembers
                    ];
                } elseif ($setting->key == StatisticsSettings::FOLLOWERSCOUNT) {
                    $followersCount = DB::table('follows')->where('following_id', '=', $field->user_id)->count();
                    $userFields[] = [
                        'user-followers-count' => $followersCount,
                    ];
                } elseif ($setting->key == StatisticsSettings::FOLLOWINGCOUNT) {
                    $followersCount = DB::table('follows')->where('follower_id', '=', $field->user_id)->count();
                    $userFields[] = [
                        'user-following-count' => $followersCount,
                    ];
                } elseif ($setting->key == StatisticsSettings::LASTNAME) {
                    $userLastName = DB::table('kycs')->where('id', '=', $field->user_id)->select('lname')->first();
                    $userFields[] = [
                        'user-lastname' => $userLastName
                    ];
                } elseif ($setting->key == StatisticsSettings::LASTSTATUSDATE) {
                    $userLastSeen = DB::table('users')->where('id', '=', $field->user_id)->select('last_seen')->first();
                    $userFields[] = [
                        'user-lastname' => Jalalian::forge($userLastSeen)->format('Y/m/d')
                    ];
                } elseif ($setting->key == StatisticsSettings::LASTSTATUSHOUR) {
                    $userLastSeen = DB::table('users')->where('id', '=', $field->user_id)->select('last_seen')->first();
                    $userFields[] = [
                        'user-lastname' => Jalalian::forge($userLastSeen)->format('H:i:s')
                    ];
                } elseif ($setting->key == StatisticsSettings::LEVEL) {
                    $levelId = DB::table('user_level')->where('user_id', $field->user_id)->select('level_id')->first();
                    $userLevel = DB::table('levels')->where('id', $levelId)->select('name', 'slug')->first();
                    $userFields[] = [
                        'user-level' => $userLevel->name,
                        'user-level-slug' => $userLevel->slug,
                    ];
                } elseif ($setting->key == StatisticsSettings::TRYCHART) {

                }
            } else {
                return $userFields;
            }
        }
        return $userFields;
    }

    /**
     * @param $userId
     * @param $statisticType
     * @param $settingId
     * @return void
     */
    public static function updateFieldsStatus($userId, $statisticType, $settingId): void
    {
        $satus = DB::table('user_statistics_setting')->where('user_id', $userId)
            ->where('statistics_type_id', $statisticType)
            ->where('statistics_settings_id', $settingId)->first();
        if ($satus->status == 1) {

            $satus->update([
                'status' => 0
            ]);
        } else {
            $satus->update([
                'status' => 1
            ]);
        }

    }


}
