<?php

namespace App\Http\Controllers\Api\V1\Statistics;

use App\Constants\StatisticsTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LevelOneActivatedStatisticsController extends Controller
{
    public $assetsStatistic, $settings, $userCustomFields;
    public $url = 'localhost:8001/api';

//    public function __construct()
//    {
//        $this->assetsStatistic = DB::table('statistics_types')
//            ->where('key', '=', StatisticsTypes::)->select('id', 'key')->first();
//
//        $this->settings = $this->settings = DB::table('statistics_settings')->select('id', 'key')->get();
//
//        $this->userCustomFields = DB::table('user_statistics_setting')->where('statistics_type_id', '=', $this->assetsStatistic->id)->where('user_id', auth('sanctum')->user()->id)->get();
//
//    }
//
//    public function index()
//    {
//        $response = Http::get($this->url . '/all-users-level-one-activated');
//        return $response->json();
//    }
}
