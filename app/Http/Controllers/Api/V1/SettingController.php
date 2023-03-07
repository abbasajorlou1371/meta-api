<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralSettingsResource;
use App\Http\Resources\SettingResource;
use App\Models\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Privacy;
use App\Http\Resources\PrivacyResource;
use App\Http\Requests\UpdatePrivacyRequest;

class SettingController extends Controller
{
    public function showSettings()
    {
        return new SettingResource(request()->user()->settings);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $settings = auth()->user()->settings;

        if ($request->has('checkout_days_count')) {
            $request->validate([
                'checkout_days_count' => 'required|integer|min:3|max:1000',
                'automatic_logout' => 'required|integer|min:1|max:55',
            ]);

            $settings->update([
                'checkout_days_count' => $request->checkout_days_count,
                'automatic_logout' => $request->automatic_logout,
            ]);
        }

        if ($request->has('setting')) {
            $request->validate([
                'setting' => 'required|in:status,level,details',
                'status' => 'required|boolean',
            ]);
            $settings->update([
                $request->input('setting') => $request->input('status'),
            ]);
        }

        return response()->noContent();
    }

    public function showGeneralSettings()
    {
        return new GeneralSettingsResource(request()->user()->generalSettings);
    }

    public function updateGeneralSettings(Request $request, GeneralSetting $generalSetting)
    {
        $this->authorize('update', $generalSetting);

        $request->validate([
            'announcements_sms' => 'required|boolean',
            'announcements_email' => 'required|boolean',
            'reports_sms' => 'required|boolean',
            'reports_email' => 'required|boolean',
            'login_verification_sms' => 'required|boolean',
            'login_verification_email' => 'required|boolean',
            'transactions_sms' => 'required|boolean',
            'transactions_email' => 'required|boolean',
            'trades_sms' => 'required|boolean',
            'trades_email' => 'required|boolean',
        ]);
        $generalSetting->update([
            'announcements_sms' => $request->announcements_sms,
            'announcements_email' => $request->announcements_email,
            'reports_sms' => $request->reports_sms,
            'reports_email' => $request->reports_email,
            'login_verification_sms' => $request->login_verification_sms,
            'login_verification_email' => $request->login_verification_email,
            'transactions_sms' => $request->transactions_sms,
            'transactions_email' => $request->transactions_email,
            'trades_sms' => $request->trades_sms,
            'trades_email' => $request->trades_email,
        ]);
        return new GeneralSettingsResource($generalSetting->refresh());
    }

    public function getPrivacySettings(Request $request)
    {
        return new PrivacyResource($request->user()->privacy);
    }

    public function updatePrivacySettings(UpdatePrivacyRequest $request)
    {
        Privacy::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'name' => $request->setting,
            ],

            [
                'display' => $request->value
            ]
        );
        return response()->noContent();
    }
}
