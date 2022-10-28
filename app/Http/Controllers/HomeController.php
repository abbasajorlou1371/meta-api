<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeResource;
use App\Http\Resources\UserResource;
use App\Models\Feature;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\PackageResource;
use App\Http\Resources\TopPlayerResource;

class HomeController extends Controller
{

    /**
     * @return array
     */
    public function index(Request $request) :array
    {
        return [
            'user' => $request->user('sanctum') ? new UserResource($request->user('sanctum')) : [],
            'packages' => PackageResource::collection(Option::all()),
            'top_players' => ! $request->user('sanctum')
            ? TopPlayerResource::collection(User::orderBy('score', 'DESC')->take(10)->lazy())  : [],
            'features' => Feature::with(['properties', 'geometry.coordinates'])->lazy()
        ];
    }

    public function showUserDetails(User $user) {
        return new TopPlayerResource($user);
    }
}
