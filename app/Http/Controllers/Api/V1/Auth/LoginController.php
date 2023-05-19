<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthenticatedUserResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Importing traits for login throttling and user authentication
    use ThrottlesLogins, AuthenticatesUsers;

    // Maximum number of login attempts allowed
    protected $maxAttempts = 3;

    // Number of minutes to wait before allowing login after maximum attempts
    protected $decayMinutes = 5;

    // This method is called when the user is successfully authenticated
    protected function authenticated(Request $request, $user)
    {
        // Perform actions when the user is logged in
        $user->logedIn(); // Custom method to update user's login status

        // Get the automatic logout setting from user's preferences
        $automaticLogout = $user->settings->automatic_logout;

        // Store the automatic logout setting in the user object
        $user->automaticLogout = $automaticLogout;

        // Calculate the token expiration time based on the automatic logout setting
        $tokenExpiresAt = now()->addMinutes($automaticLogout > 0 ? $automaticLogout : 60);

        // Create a new token for the user using Laravel Sanctum and store it in the user object
        $user->token = $user->createToken('token-' . $user->id, expiresAt: $tokenExpiresAt)->plainTextToken;

        // Return the authenticated user as a resource
        return new AuthenticatedUserResource($user);
    }

    // This method is called when the user is logged out
    protected function loggedOut(Request $request)
    {
        // Revoke all tokens associated with the logged-out user
        $request->user()->tokens()->delete();

        // Perform actions when the user is logged out
        $request->user()->logedOut(); // Custom method to update user's logout status

        // Return a response with no content
        return response()->noContent();
    }

    // Get the authentication guard to use
    protected function guard()
    {
        return Auth::guard('web');
    }
}
