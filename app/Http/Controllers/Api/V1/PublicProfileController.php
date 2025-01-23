<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\PublicProfile\PersonalInfo;
use App\Http\Resources\ReferralResource;

class PublicProfileController extends Controller
{
    /**
     * Get user public profile
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
     * Get user's referrals
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function referrals(Request $request, User $user)
    {
        $query = $user->referrals()->with(['referrerOrders' => function ($query) {
            $query->latest();
        }, 'kyc:id,user_id,fname,lname', 'latestProfilePhoto'])
            ->orderBy(function ($query) {
                $query->select('created_at')
                    ->from('referral_order_histories')
                    ->whereColumn('referral_id', 'users.referrer_id')
                    ->latest()
                    ->limit(1);
            }, 'desc');

        $query->when($request->has('search'), function ($q) use ($request) {
            $search = $request->input('search');
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        });

        $referrals = $query->simplePaginate(10);

        return ReferralResource::collection($referrals);
    }

    /**
     * Get referral referral chart data
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \App\Http\Resources\ReferralChartDataResource
     */
    public function referralChart(Request $request, User $user)
    {
        $range = $request->input('range', 'daily');
        $startDate = $this->getStartDate($range);

        $referrals = $user->referrals()
            ->select(['id', 'name', 'referrer_id', 'created_at'])
            ->where('created_at', '>=', $startDate)
            ->with('referrerOrders')
            ->get();

        $groupedReferrals = $this->groupReferralsByRange($referrals, $range);

        $totalReferrals = $referrals->count();
        $totalReferrerOrderAmount = $referrals->sum(function ($referral) {
            return $referral->referrerOrders->sum('amount');
        });

        $referralsData = $groupedReferrals->map(function ($group) use ($range) {
            return $group->map(function ($referral) use ($range) {
                $createdAt = $referral->created_at;
                if ($range === 'yearly') {
                    $createdAt = $createdAt->format('Y');
                } elseif ($range === 'monthly') {
                    $createdAt = $createdAt->format('Y-m');
                } elseif ($range === 'weekly') {
                    $createdAt = $createdAt->format('Y-m-d');
                } else {
                    $createdAt = $createdAt->format('Y-m-d H:i:s');
                }
                return [
                    'id' => $referral->id,
                    'name' => $referral->name,
                    'created_at' => $createdAt,
                    'total_order_amount' => $referral->referrerOrders->sum('amount'),
                ];
            });
        });

        return response()->json(['data' => [
            'total_referrals' => $totalReferrals,
            'total_referrer_order_amount' => $totalReferrerOrderAmount,
            'referrals' => $referralsData,
        ]]);
    }

    private function getStartDate($range)
    {
        return match ($range) {
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonths(12),
            'yearly' => now()->subYears(1),
            'daily' => now()->subDay(),
            default => now()->subDay(),
        };
    }

    private function groupReferralsByRange($referrals, $range)
    {
        return match ($range) {
            'daily' => $referrals->groupBy(fn($date) => $date->created_at->format('Y-m-d H:00')),
            'weekly' => $referrals->groupBy(fn($date) => $date->created_at->format('Y-m-d')),
            'monthly' => $referrals->groupBy(fn($date) => $date->created_at->format('Y-m')),
            'yearly' => $referrals->groupBy(fn($date) => $date->created_at->format('Y')),
            default => $referrals->groupBy(fn($date) => $date->created_at->format('Y-m-d H:00')),
        };
    }
}
