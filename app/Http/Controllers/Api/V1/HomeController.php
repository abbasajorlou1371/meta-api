<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Http\Resources\PackageResource;
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
}
