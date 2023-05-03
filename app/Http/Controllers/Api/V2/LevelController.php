<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Level\GemResource;
use App\Http\Resources\V2\Level\GeneralInfoResource;
use App\Http\Resources\V2\Level\GiftResource;
use App\Http\Resources\V2\Level\LevelResource;
use App\Http\Resources\V2\Level\LicensesResource;
use App\Http\Resources\V2\Level\PrizeResource;
use App\Models\Level\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        return LevelResource::collection(Level::with('image')->get());
    }

    public function show(Level $level)
    {
        return new LevelResource($level);
    }

    public function getGeneralInfo(Level $level)
    {
        return new GeneralInfoResource($level->load('generalInfo'));
    }

    public function gift(Level $level)
    {
        return new GiftResource($level->load('gift'));
    }

    public function gem(Level $level)
    {
        return new GemResource($level->load('gem'));
    }

    public function licenses(Level $level)
    {
        return new LicensesResource($level->load('licenses'));
    }

    public function prizes(Level $level)
    {
        return new PrizeResource($level->load('prizes'));
    }
}
