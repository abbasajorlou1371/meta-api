<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Http\Resources\PackageResource;
use App\Models\Ip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return PackageResource
     */
    public function getStorePackages(Request $request)
    {
        $request->validate([
            'codes' => 'required|array|min:2',
            'codes.*' => 'required|string|min:2'
        ]);
        return PackageResource::collection(
            Option::whereIn('code', $request->codes)->get()
        );
    }

    public function sendIpToSupport(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'email' => 'required|email'
        ]);

        Ip::create([
            'title' => 'آی پی مسدود شده',
            'type' => 'api',
            'from' => ip2long($request->ip),
            'email' => $request->email,
            'blocked' => 1
        ]);

        return new JsonResponse([], 200);
    }
}
