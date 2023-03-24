<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Http\Resources\PackageResource;
use App\Http\Resources\VideoTutorialResource;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'codes' => 'required|array|min:2',
            'codes.*' => 'required|string|min:2'
        ]);
        return PackageResource::collection(
            Option::whereIn('code', $request->codes)->get()
        );
    }

    public function tutorials(Request $request)
    {
        $request->validate(['url' => 'required|string']);
        $tutorial = Video::select(['title', 'description', 'fileName', 'image', 'creator_code'])
            ->where('fileName', 'like', $request->url . '%')->first();
        return $tutorial ? new VideoTutorialResource($tutorial) : [];
    }
}
