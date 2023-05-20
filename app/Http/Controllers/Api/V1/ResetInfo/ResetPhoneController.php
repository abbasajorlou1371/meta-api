<?php

namespace App\Http\Controllers\Api\V1\ResetInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetInfoRequest;
use App\Models\Reset;
use Illuminate\Http\Request;
use App\Notifications\GetOtpNotification;
use Illuminate\Support\Facades\Hash;

class ResetPhoneController extends Controller
{
    /**
     * Send the verification code to the user's phone number.
     *
     * @param ResetInfoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerifyCode(ResetInfoRequest $request)
    {
        $user = $request->user();

        // Create a new reset request for the user's phone number
        $reset = Reset::create([
            'user_id' => $user->id,
            'type' => 'phone',
            'value' => $request->phone,
        ]);

        // Generate a random verification code
        $code = random_int(100000, 999999);

        // Create an OTP (One-Time Password) record for the reset request
        $reset->otp()->create([
            'user_id' => $user->id,
            'code' => Hash::make($code)
        ]);

        // Send the verification code to the user's phone number
        $user->notify(new GetOtpNotification($code, phone: $request->phone));

        return response()->noContent(200);
    }

    /**
     * Verify the verification code provided by the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|integer']);
        $user = $request->user();

        // Get the latest reset request for the user
        $reset = $user->latestResetRequest;

        // If no reset request exists or it has already been verified, return an error
        abort_if(is_null($reset) || $reset->verified == 1, 401, 'Not Valid');

        // Check if the provided code matches the hashed code stored in the reset request
        if (Hash::check($request->code, $reset->otp->code)) {
            // Update the user's phone number and mark it as verified
            $user->update([
                'phone' => $reset->value,
                'phone_verified_at' => now(),
            ]);

            // Mark the reset request as verified
            $reset->update(['verified' => true]);

            // Delete the OTP record associated with the reset request
            $reset->otp->delete();

            return response()->noContent(200);
        }

        return response()->noContent(400);
    }
}
