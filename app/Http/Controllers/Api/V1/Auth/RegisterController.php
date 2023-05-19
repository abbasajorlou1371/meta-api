<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Referal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthenticatedUserResource;

class RegisterController extends Controller
{
    // Register a new user
    public function register(RegisterUserRequest $request)
    {
        // Create a new user in the database with the provided data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Call the registered method to perform additional actions after registration
        return $this->registered($request, $user);
    }

    // Perform actions after a user has been successfully registered
    protected function registered(RegisterUserRequest $request, $user)
    {
        // Check if there is a referral code provided in the request
        if ($request->referral) {
            // Find the user with the provided referral code
            $reference_user = User::firstWhere('code', $request->referral);

            // Create a referral record linking the reference user and the registered user
            Referal::create([
                'reference_id' => $reference_user->id,
                'referer_id' => $user->id,
            ]);
        }

        // Perform actions when the user is registered
        $user->registered(); // Custom method to update user's registration status

        // Get the automatic logout setting from user's preferences
        $automaticLogout = $user->settings->automatic_logout;

        // Store the automatic logout setting in the user object
        $user->automaticLogout = $automaticLogout;

        // Calculate the token expiration time based on the automatic logout setting
        $tokenExpiresAt = now()->addMinutes($automaticLogout > 0 ? $automaticLogout : 60);

        // Create a new token for the user using Laravel Sanctum and store it in the user object
        $user->token = $user->createToken('token-' . $user->id, expiresAt: $tokenExpiresAt)->plainTextToken;

        // Log the user in
        $this->guard()->login($user);

        // Return the authenticated user as a resource
        return new AuthenticatedUserResource($user);
    }

    // Get the authentication guard to use
    protected function guard()
    {
        return Auth::guard('web');
    }
}
