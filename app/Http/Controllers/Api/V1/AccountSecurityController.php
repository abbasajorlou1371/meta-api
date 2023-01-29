<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\AccountSecurityRequest;
use App\Notifications\GetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AccountSecurityController extends Controller
{
    public function getVerifyCode(AccountSecurityRequest $request)
    {

        $user = $request->user();
        $accountSecurity = $user->accountSecurity;
        $code = random_int(100000, 999999);
        if (!$user->accountSecurity) {
            $accountSecurity = $user->accountSecurity()->create([
                'length' => $request->time * 60,
            ]);
        } else {
            $accountSecurity->update([
                'unlocked' => false,
                'until' => null,
                'length' => $request->time * 60,
            ]);
        }

        if (is_null($user->phone)) {
            $user->update(['phone' => $request->phone]);
        }

        $accountSecurity->otp()->updateOrCreate(
            ['user_id' => $user->id],
            ['code' => Hash::make($code)]
        );
        $user->notify(new GetOtpNotification($code, $user->phone ?: $request->phone));
        return response()->json(['message' => 'کد تایید ارسال گردید. جهت ادامه کد تایید را وارد کنید!'], 200);
    }

    public function turnOffAccountSecurity(Request $request)
    {
        $user = $request->user();
        $this->validate(
            $request,
            ['code' => 'required|integer|min:100000|max:999999'],
            [
                'code.required' => 'کد تایید را وارد کنید',
                'code.integer' => 'کد تایید صحیح نیست',
                'code.min' => 'کد تایید صحیح نیست',
                'code.max' => 'کد تایید صحیح نیست'
                ]
            );
        $accountSecurity = $user->accountSecurity;
        if(!$accountSecurity || !$accountSecurity->otp) {
            abort(404, 'درخواست معتبر نمی باشد!');
        } elseif($accountSecurity->unlocked) {
            abort(404, 'درخواست معتبر نمی باشد!');
        }
        if(Hash::check($request->code, $accountSecurity->otp->code)) {
            if(is_null($user->phone_verified_at)) {
                $user->update(['phone_verified_at' => now()]);
            }
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
            return response()->json([
                'message' => sprintf('امنیت حساب کاربری شما به مدت %s دقیقه غیر فعال گردید.', $accountSecurity->length / 60)
            ], 200);
        } else {
            throw ValidationException::withMessages([
                'error' => 'کد تایید صحیح نیست!'
            ]);
        }
    }
}
