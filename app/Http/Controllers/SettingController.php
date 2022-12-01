<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $settings = auth()->user()->settings;

        if ($request->has('checkout_days_count')) {
            $request->validate([
                'checkout_days_count' => 'required|numeric|min:3',
                'automatic_logout' => 'required|min:0',
            ]);

            $settings->update([
                'checkout_days_count' => $request->checkout_days_count,
                'automatic_logout' => $request->automatic_logout,
            ]);
        }

        if ($request->has('setting')) {
            $settings->update([
                $request->input('setting') => $request->input('status'),
            ]);
        }

        return response()->json([
            'success' => 'تنظیمات بروز رسانی شد'
        ], 200);
    }

    public function generalSettingsUpdate(Request $request)
    {
        $request->user()->generalSettings->update([
            $request->input('setting') => $request->input('status'),
        ]);
        return response()->json([
            'message' => 'تنظیمات بروزرسانی شد'
        ], 200);
    }


    public function uploadProfilePhoto(Request $request)
    {
        $this->validate(
            $request,
            ['image' => 'required|file|mimes:png,jpg'],
            [
                'image.required' => 'تصویری برای بارگذاری انتخاب کنید',
                'image.mimes' => 'فرمت فایل صحیح نمی باشد'
            ]

        );
        $url = env('FTP_ENDPOINT') . $request->file('image')->store('/user/profile/' . $request->user()->id);
        $request->user()->profilePhotos()->create([
            'url' => $url
        ]);
        return response()->json([
            'message' => 'تصویر بارگذاری شد'
        ], 200);
    }
<<<<<<< HEAD

    public function sendPhoneVerificationOtp(Request $request)
    {
        if ($request->user()->phone) {
            abort(403, 'کاربر قبلا شماره تلفن خود را ثبت کرده است');
        }

        if (Cache::has('verify-phone-' . $request->user()->id)) {
            abort(403, 'کد تایید قبلا ارسال شده است');
        }

        $this->validate(
            $request,
            [
                'phone' => 'required|ir_mobile',
            ],
            [

                'phone.required' => 'شماره تلفن را وارد کنید',
                'phone.ir_mobile' => 'شماره تلفن صحیح نمی باشد'
            ]
        );

        $data = [
            'phone' => $request->phone,
            'code' => random_int(100000, 999999)
        ];
        Cache::put('verify-phone-' . $request->user()->id, $data, now()->addMinutes(5));
        $request->user()->notify(new GetOtpNotification($request->phone, $data['code']));

        return response()->json(['success' => 'کد تایید ارسال گردید'], 200);
    }

    public function verifyPhone(Request $request)
    {
        $this->validate(
            $request,
            ['code' => 'required|integer|numeric'],
            [
                'code.required' => 'کد تایید را وارد کنید',
                'code.integer' => 'کد تایید وارد شده صحیح نیست',
            ]
        );

        $cachedData = Cache::get('verify-phone-' . $request->user()->id);

        if (!$cachedData || $cachedData['code'] != $request->code) {
            abort(401, 'کد تایید وارد صحیح نیست');
        }

        $request->user()->update([
            'phone' => $cachedData['phone']
        ]);
        return response()->json([
            'success' => 'شماره تلفن ثبت شد'
        ]);
    }
=======
>>>>>>> bf9baa8490005faa41d71c71545c971d4b4f081f
}
