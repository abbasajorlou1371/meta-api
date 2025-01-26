<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\PublicProfile\PersonalInfo;
use App\Http\Resources\ReferralResource;
use App\Services\ReferralService;

class PublicProfileController extends Controller
{
    public function __construct(
        protected ReferralService $referralService
    ) {}

    /**
     * Get user public profile.
     *
     * @param \App\Models\User $user
     * @return \App\Http\Resources\PublicProfile\PersonalInfo
     */
    public function home(User $user)
    {
        $user->load(['kyc', 'personalInfo', 'profilePhotos', 'settings:id,user_id,privacy']);
        return new PersonalInfo($user);
    }

    /**
     * Get user's referrals.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function referrals(Request $request, User $user)
    {
        $referrals = $this->referralService->getReferrals($request, $user);
        return ReferralResource::collection($referrals);
    }

    /**
     * Get referral chart data.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function referralChart(Request $request, User $user)
    {
        $range = $request->input('range', 'daily');

        $referrals = $user->referrals()
            ->select(['id', 'name', 'referrer_id', 'created_at'])
            ->with(['referrerOrders' => function ($query) {
                $query->select('id', 'referral_id', 'amount', 'created_at');
            }])
            ->get();

        return match ($range) {
            'yearly' => response()->json(['data' => $this->referralService->getYearlyStats($referrals)]),
            'monthly' => response()->json(['data' => $this->referralService->getMonthlyStats($referrals)]),
            'weekly' => response()->json(['data' => $this->referralService->getWeeklyStats($referrals)]),
            'daily' => response()->json(['data' => $this->referralService->getDailyStats($referrals)]),
            default => response()->json(['data' => $this->referralService->getDailyStats($referrals)]),
        };
    }
}
