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
    public function sendVerifyCode(AccountSecurityRequest $request)
    {
        $user = $request->user();

        $accountSecurity = $user->accountSecurity;

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

        $accountSecurity->otp()->updateOrCreate(
            ['user_id' => $user->id],
            ['code' => Hash::make($code)]
        );

        $user->notify(new GetOtpNotification($code, phone: $user->phone ?: $request->phone));

        return response()->noContent(200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = $request->user();

        $accountSecurity = $user->accountSecurity;

        // Check if the user has an account security and the account security is locked
        abort_if(!$accountSecurity || !$accountSecurity->otp, 400);
        abort_if($accountSecurity->unlocked, 400);

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

            $accountSecurity->otp->delete();

            $user->events()->create([
                'event' => "غیر فعال سازی امنیت حساب کاربری",
                'ip' => $request->ip(),
                'device' => $request->userAgent(),
                'status' => 1,
            ]);
            
            return response()->noContent(200);
        } else {
            throw ValidationException::withMessages([
                'code' => 'کد تایید صحیح نیست!'
            ]);
        }
    }
}
