<?php

namespace App\Http\Controllers\Api\V1\ResetInfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ResetInfoRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Reset;
use App\Notifications\GetOtpNotification;
use Illuminate\Validation\ValidationException;

class ResetEmailController extends Controller
{
    /**
     * @param ResetInfoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerifyCode(ResetInfoRequest $request)
    {
        $user = $request->user();

        // Create a new reset request
        $reset = Reset::create([
            'user_id' => $user->id,
            'type' => 'email',
            'value' => $request->email,
        ]);

        // Generate a random code
        $code = random_int(100000, 999999);

        // Create a new otp
        $reset->otp()->create([
            'user_id' => $user->id,
            'code' => Hash::make($code)
        ]);

        // Send the code to user
        $user->notify(new GetOtpNotification($code, 'mail', email: $request->email));
        return response()->noContent(200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws ValidationException
     */
    public function verify(Request $request)
    {
        // Validate the request
        $request->validate(['code' => 'required|integer|digits:6']);
        $user = $request->user();

        // Get the latest reset request
        $reset = $user->latestResetRequest;

        // Check if the reset request is valid
        abort_if(is_null($reset) || $reset->verified == 1, 401, 'Not Valid');

        // Check if the code is correct
        if(Hash::check($request->code, $reset->otp->code)) {
            // Update the user's email
            $user->update([
                'email' => $reset->value,
                'email_verified_at' => now(),
            ]);

            // Update the reset request
            $reset->update(['verified' => true]);

            // Delete the otp
            $reset->otp->delete();
            return response()->noContent(200);
        } else {
            // Throw an exception if the code is wrong
            throw ValidationException::withMessages(['code' => 'کد تایید اشتباه است.']);
        }
    }
}
