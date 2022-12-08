<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\PublicProfile\PersonalInfo;

class PublicProfileController extends Controller
{
    public function home(Request $request, string $code) {
        $user = User::with('kyc', 'customs', 'customs.passions', 'profilePhotos', 'level')
        ->where('code', $code)->first();
        if(!$user) {
            return response()->json(['error' => 'کاربر یافت نشد'], 404);
        }
        return new PersonalInfo($user);
    }
}
