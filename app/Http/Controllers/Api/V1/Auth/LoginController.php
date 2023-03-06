<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Setting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ThrottlesLogins, AuthenticatesUsers;

    protected $maxAttempts = 3;
    protected $decayMinutes = 5;

    protected function authenticated(Request $request, $user)
    {
        $user->logedIn();
        $automaticLogout = Setting::whereUserId($user->id)->pluck('automatic_logout')->first();
        $tokenExpiresAt = now()->addMinutes($automaticLogout > 0 ? $automaticLogout : 60);
        $user->token = $user->createToken('token-' . $user->id, expiresAt: $tokenExpiresAt)->plainTextToken;
        return new UserResource($user);
    }

    protected function loggedOut(Request $request)
    {
        $request->user()->tokens()->delete();
        $request->user()->logedOut();
        return response()->noContent();
    }

    protected function guard()
    {
        return Auth::guard('web');
    }
}
