<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\AccountSecurityRequest;
use App\Notifications\GetOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountSecurityController extends Controller
{
    /**
     * @param AccountSecurityRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getVerifyCode(AccountSecurityRequest $request)
    {
        $user = $request->user();

        // Get the user's account security
        $accountSecurity = $user->accountSecurity;

        // Generate a random code
        $code = random_int(100000, 999999);

        // If the user does not have an account security, create it
        if (is_null($accountSecurity)) {
            $accountSecurity = $user->accountSecurity()->create([
                'length' => $request->time * 60,
            ]);
        } else {
            // If the user has an account security, update it
            $accountSecurity->update([
                'unlocked' => false,
                'until' => null,
                'length' => $request->time * 60,
            ]);
        }

        // If the user does not have a phone number, update it
        if (!$user->hasVerifiedPhone()) {
            $user->update(['phone' => $request->phone]);
        }

        // Create or update the user's otp
        $accountSecurity->otp()->updateOrCreate(
            ['user_id' => $user->id],
            ['code' => Hash::make($code)]
        );

        // Send the code to the user
        $user->notify(new GetOtpNotification($code, phone: $user->phone ?: $request->phone));
        return response()->noContent(200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function turnOffAccountSecurity(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = $request->user();

        // Get the user's account security
        $accountSecurity = $user->accountSecurity;

        // Check if the user has an account security and the account security is locked
        abort_if(!$accountSecurity || !$accountSecurity->otp, 400);
        abort_if($accountSecurity->unlocked, 400);

        // Check if the code is correct
        if (Hash::check($request->code, $accountSecurity->otp->code)) {
            // If the user does not have a phone number, update it
            if (is_null($user->phone_verified_at)) {
                $user->update(['phone_verified_at' => now()]);
            }

            // Update the user's account security
            $accountSecurity->update([
                'unlocked' => true,
                'until' => time() + $accountSecurity->length,
            ]);

            // Delete the user's otp
            $accountSecurity->otp->delete();

            // Create a log for the user
            $user->events()->create([
                'event' => "غیر فعال سازی امنیت حساب کاربری",
                'ip' => $request->ip(),
                'device' => $request->userAgent(),
                'status' => 1,
            ]);
            return response()->noContent(200);
        } else {
            // Throw an exception if the code is incorrect
            throw ValidationException::withMessages([
                'code' => 'کد تایید صحیح نیست!'
            ]);
        }
    }
}
