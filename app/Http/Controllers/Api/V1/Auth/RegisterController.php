<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Referal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthenticatedUserResource;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->saveQuietly();

        return $this->registered($request, $user);
    }

    protected function registered(RegisterUserRequest $request, $user)
    {
        if ($request->referral) {
            $reference_user = User::firstWhere('code', $request->referral);

            Referal::create([
                'reference_id' => $reference_user->id,
                'referer_id' => $user->id,
            ]);
        }

        $user->registered();

        $automaticLogout = $user->settings->automatic_logout;

        $user->automaticLogout = $automaticLogout;

        $tokenExpiresAt = now()->addMinutes($automaticLogout > 0 ? $automaticLogout : 60);

        $user->token = $user->createToken('token-' . $user->id, expiresAt: $tokenExpiresAt)->plainTextToken;

        $this->guard()->login($user);

        $request->session()->regenerate();

        return new AuthenticatedUserResource($user);
    }

    /**
     * Get the guard to be used during registration.
     * 
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
