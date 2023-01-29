<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\PublicProfile\PersonalInfo;

class PublicProfileController extends Controller
{
    public function home(Request $request, string $code) {
        $user = User::with('kyc', 'customs', 'customs.passions', 'profilePhotos', 'level')
        ->where('code', $code)->first();
        return $user ? new PersonalInfo($user) : response()->noContent(404);
    }
}
