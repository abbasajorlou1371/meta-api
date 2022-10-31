<?php

namespace App\Http\Controllers\ResetInfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Reset\ResetEmail;
use App\Notifications\SendVerifyEmailCode;

class ResetEmailController extends Controller
{
    public function sendOtpToOldEmail(Request $request)
    {
        $resetEmail = ResetEmail::where('user_id', $request->user()->id)->first();

        if($resetEmail)
        {
            if($resetEmail->count() > 0)
            {
                abort(403, 'شما فقط یکبار مجاز به تغییر ایمیل خود می باشید');
            }
        }

        if(Cache::has('reset-email-old-email-verification-'. $request->user()->id))
        {
            abort(403, 'کد تایید قبلا ارسال شده است');
        }

        $this->validate(
            $request,
            [
                'email' => 'required|email|unique:users',
            ],
            [

                'email.required' => 'شماره تلفن را وارد کنید',
                'email.email' => 'آدرس ایمیل صحیح نیست',
                'email.unique' => 'ایمیل قبلا استفاده شده است'
            ]
        );

        $data = [
            'email' => $request->email,
            'code' => random_int(100000, 999999)
        ];
        Cache::put('reset-email-old-email-verification-'. $request->user()->id, $data, now()->addMinutes(5));
        $request->user()->notify(new SendVerifyEmailCode(null, $data['code']));

        return response()->json(['success' => 'کد تایید به آدرس ایمیل قبلی ارسال گردید'], 200);
    }

    public function verifyOldEmailOtp(Request $request)
    {
        $this->validate(
            $request,
            ['code' => 'required|integer|numeric'],
            [
                'code.required' => 'کد تایید را وارد کنید',
                'code.integer' => 'کد تایید وارد شده صحیح نیست',
            ]
        );

        $cachedData = Cache::get('reset-email-old-email-verification-'. $request->user()->id);

        if (!$cachedData || $cachedData['code'] != $request->code) {
            abort(401, 'کد تایید وارد صحیح نیست');
        }

        $data = [
            'email' => $cachedData['email'],
            'code' => random_int(100000, 999999)
        ];
        Cache::put('new-email-verification-'. $request->user()->id, $data, now()->addMinutes(5));

        Cache::forget('reset-email-old-email-verification-'. $request->user()->id);
        $request->user()->notify(new SendVerifyEmailCode($cachedData['email'], $data['code']));
        return response()->json(['success' => 'کد تاییدی به آدرس ایمیل جدید ارسال گردید'], 200);
    }

    public function verifyNewEmailOtp(Request $request)
    {
        $this->validate(
            $request,
            ['code' => 'required|integer|numeric'],
            [
                'code.required' => 'کد تایید را وارد کنید',
                'code.integer' => 'کد تایید وارد شده صحیح نیست',
            ]
        );

        $cachedData = Cache::get('new-email-verification-'. $request->user()->id);

        if (!$cachedData || $cachedData['code'] != $request->code) {
            abort(401, 'کد تایید وارد صحیح نیست');
        }

        $request->user()->update([
            'email' => $cachedData['email']
        ]);

        $request->user()->resetEmail()->updateOrCreate([
            'count' => 1
        ]);

        return response()->json(['success' => 'آدرس ایمیل تغییر کرد'], 200);
    }
}
