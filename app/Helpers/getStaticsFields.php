<?php

use Illuminate\Support\Facades\DB;

function customGetFields($type, $settings, $user_id)
{
    return DB::table('user_statistics_setting')->where('user_id', '=', $user_id)
        ->where('statistics_type_id', '=', $type)
        ->where('statistics_settings_id', '=', $settings)->pluck('status')->first();
}


function adminCustomFields($type, $settings)
{
    return DB::table('statistics_type_setting')
        ->where('statistics_type_id', '=', $type->id)
        ->where('statistics_setting_id', '=', $settings)->pluck('status')->first();
}
