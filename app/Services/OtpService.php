<?php

namespace App\Services;

use App\Models\Otp;
use App\Notifications\GetOtpNotification;
use Illuminate\Validation\ValidationException;

class OtpService {
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getOtpCode(Request $request)
    {
        $user = $request->user();
        $this->validate(
            $request,
            [
                'phone' => 'nullable|ir_mobile',
                'otp_reason' => 'required|string|in:trade-feature,trade-feature-sms'
            ],
            [
                'phone.required' => 'شماره تلفن را وارد کنید',
                'phone.ir_mobile' => 'شماره تلفن صحیح نیست'
            ]
        );

        $otp = Otp::where('user_id', $user->id)->where('otp_reason', $request->otp_reason)->first();

        if($otp) {
            if($otp->updated_at->diffInMinutes(now()) < 2) {
                $remainingTime = $otp->updated_at->addMinutes(2)->diffInMinutes(now());
                return response()->json([
                    'code' => 400,
                    'message' => 'کد درخواست قبلا برای شما ارسال گردیده است لطفا بعد از ' . $remainingTime . ' دوباره تلاش کنید'
                ]);
            }
        }

        $otp = Otp::updateOrCreate(
            [
            'user_id' => $user->id,
            'otp_reason' => $request->otp_reason,
            ],
            [
                'code' => random_int(100000, 999999)
            ]
        );

        if ($request->has('phone')) {
            $phone = $request->phone;
        } else {
            if(! $request->user()->phone) {
                abort(404, 'کاربر شما تلفن همراه ثبت شده ای ندارد');
            }
            $phone = $request->user()->phone;
        }
        $user->notify(new GetOtpNotification($phone, $otp->code));
        return response()->json([
            'success' => 'کد تایید به شماره تلفن همراه شما ارسال گردید'
        ]);
    }

    public function verifyOtpCode(Request $request) {
        $this->validate(
            $request,
            [
                'code' => 'required|integer',
                'otp_reason' => 'required|string'
            ],
            [
                'code.required' => 'کد تایید را وارد کنید',
                'code.integer' => 'کد تایید صحیح نیست'
            ]
        );
        $user = $request->user();
        $otp = $user->otp->where('otp_reason', $request->otp_reason)->first();

        if($otp->code != $request->code || $otp->updated_at->diffInMinutes(now()) > 5) {
            throw ValidationException::withMessages([
                'status' => '400',
                'message' => 'کد تایید وارد شده صحیح نیست یا منقضی شده است'
            ]);
        }
        return response()->json([
            'status' => '200',
            'message' => 'کد تایید گردید',
            'code' => $request->code
        ]);
    }
}
