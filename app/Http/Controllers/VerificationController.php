<?php

namespace App\Http\Controllers;

use App\Models\Reset\ResetEmail;
use App\Models\Reset\ResetPhone;
use App\Notifications\SendVerificationCode;
use App\Notifications\SendVerifyEmailCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    public function sendPhoneVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|ir_mobile'
        ]);

        $user = $request->user();
        $reset = ResetPhone::firstWhere('user_id', $user->id);

        if (isset($reset)) {
            if($reset->count > 0)
            {
                return response()->json([
                    'error' => 'شما فقط یکبار مجاز به تغییر شماره تلفن خود میباشید'
                ]);
            }
        }
        $reset = ResetPhone::updateOrCreate(
            ['user_id' => $user->id],
            [
            'phone' => $request->phone,
            'code' => random_int(100000, 999999),
            'expires' => now()->addMinutes(5)
            ]
        );
        $user->notify(new SendVerificationCode($reset));
        return response()->json([
            'success' => 'کد تایید برای شما ارسال شد'
        ]);
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'code' => 'required|integer'
        ]);

        $user = $request->user();
        $reset = ResetPhone::firstWhere('user_id', $user->id);

        if (isset($reset)) {
            if (
                $reset->code !== $request->code ||
                Carbon::parse($reset->expires) < Carbon::now()
            ) {
                throw ValidationException::withMessages([
                    'error' => 'کد تایید وارد شده صحیح نیست و یا منقضی شده است'
                ]);
            }
            $user->update([
                'phone' => $reset->phone,
            ]);

            $reset->update([
                'count' => $reset->count+=1,
                'expires' => now(),
            ]);
            return response()->json([
                'success' => 'شماره تلفن تغییر یافت'
            ]);
        }

        return response()->json([
            'error' => 'خطایی رخ داده است. لطفا بعدا تلاش کنید'
        ]);
    }

    public function sendEmailVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = $request->user();

        $reset = ResetEmail::firstWhere('user_id', $user->id);

        if (isset($reset)) {
            if ($reset->count > 0) {
                return response()->json([
                    'error' => 'شما فقط یکبار مجاز به تغییر ایمیل خود هستید'
                ]);
            }
        }

        $reset = ResetEmail::updateOrCreate(
            ['user_id' => $user->id],
            [
            'email' => $request->email,
            'code' => random_int(100000, 999999),
            'expires' => now()->addMinutes(5)
            ]
        );
        $user->notify(new SendVerifyEmailCode($reset));
        return response()->json([
            'success' => 'کد تایید برای به ایمیلتان ارسال گردید'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|integer'
        ]);
        $user = $request->user();
        $reset = ResetEmail::firstWhere('user_id', $user->id);

        if (isset($reset)) {
            if (
                $reset->code !== $request->code ||
                Carbon::parse($reset->expires) < Carbon::now()
            ) {
                throw ValidationException::withMessages([
                    'error' => 'کد تایید وارد شده صحیح نیست و یا منقضی شده است'
                ]);
            }
            $user->update([
                'email' => $reset->email,
            ]);

            $reset->update([
                'count' => $reset->count+=1,
                'expires' => now(),
            ]);
            return response()->json([
                'success' => 'ایمیل شما تغییر داده شد'
            ]);
        }

        return response()->json([
            'error' => 'خطایی رخ داده است. لطفا بعدا تلاش کنید'
        ]);
    }
}
