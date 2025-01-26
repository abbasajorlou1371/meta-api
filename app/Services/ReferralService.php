<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Variable;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class ReferralService
{
    /**
     * Handles the referral logic when an order is placed.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return void
     */
    public static function referral(User $user, Order $order)
    {
        $user->load('referred');

        if ($user->referred) {
            // If the asset is 'irr', do not proceed with referral
            if ($order->asset == 'irr') {
                return;
            }

            $psc_price = Variable::getRate('psc');
            $referred = $user->referred;

            // Calculate the total amount referred by the referred user
            $referred_amount = $referred->referalOrders()->sum('amount') * $psc_price ?? 0;

            // Calculate the referral amount for the referer based on the order asset
            if (in_array($order->asset, ['blue', 'red', 'yellow'])) {
                $referrer_amount = (($order->amount * Variable::getRate($order->asset)) / $psc_price) * 0.5;
            } else {
                $referrer_amount = $order->amount * 0.5;
            }

            $referralLimit = $referred->variables;

            // Check if the referred user has reached the referral profit limit
            if ($referred_amount >= $referralLimit->referral_profit) {
                return;
            }

            // Increment the referer's 'psc' asset with the referral amount
            $referred->wallet->increment('psc', $referrer_amount);

            // Create a new referal orders entry
            $referred->referralOrders()->create([
                'referral_id' => $user->id,
                'amount' => $referrer_amount,
            ]);
        }
    }

    /**
     * Returns paginated user referrals.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getReferrals(Request $request, User $user)
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

        return $query->simplePaginate(10);
    }

    /**
     * Returns yearly referral stats.
     *
     * @param \Illuminate\Support\Collection $referrals
     * @return array
     */
    public function getYearlyStats($referrals)
    {
        $totalReferralsCount = $referrals->count();
        $totalOrdersAmount = $referrals->sum(fn($r) => $r->referrerOrders->sum('amount'));

        $firstYear = $referrals->min('created_at') ? Jalalian::fromCarbon($referrals->min('created_at'))->getYear() : Jalalian::now()->getYear();
        $currentYear = Jalalian::now()->getYear();

        $chartData = collect(range($firstYear, $currentYear))->map(function ($year) use ($referrals) {
            $yearReferrals = $referrals->filter(function ($referral) use ($year) {
                return Jalalian::fromCarbon($referral->created_at)->getYear() == $year;
            });

            return [
                'year' => (string)$year,
                'total_referrals_count' => $yearReferrals->count(),
                'total_referral_orders_amount' => $yearReferrals->sum(fn($r) => $r->referrerOrders->sum('amount')),
            ];
        });

        return [
            'total_referrals_count' => (string)$totalReferralsCount,
            'total_referral_orders_amount' => (string)$totalOrdersAmount,
            'chart_data' => $chartData
        ];
    }

    /**
     * Returns monthly referral stats.
     *
     * @param \Illuminate\Support\Collection $referrals
     * @return array
     */
    public function getMonthlyStats($referrals)
    {
        $now = now();
        $monthlyReferrals = $referrals->filter(fn($r) => $r->created_at->greaterThanOrEqualTo($now->copy()->subMonths(12)));

        $totalReferralsCount = $monthlyReferrals->count();
        $totalOrdersAmount = $monthlyReferrals->sum(fn($r) => $r->referrerOrders->sum('amount'));

        $months = collect(range(0, 11))->map(function ($i) use ($now) {
            return $now->copy()->subMonths($i);
        });

        $chartData = $months->map(function ($date) use ($monthlyReferrals) {
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthReferrals = $monthlyReferrals->filter(function ($referral) use ($monthStart, $monthEnd) {
                return $referral->created_at->between($monthStart, $monthEnd);
            });

            return [
                'month' => Jalalian::fromCarbon($date)->format('%B %Y'),
                'referrals_count' => $monthReferrals->count(),
                'referral_orders_amount' => $monthReferrals->sum(fn($r) => $r->referrerOrders->sum('amount')),
            ];
        })->reverse()->values();

        return [
            'total_referrals_count' => (string)$totalReferralsCount,
            'total_referral_orders_amount' => (string)$totalOrdersAmount,
            'chart_data' => $chartData
        ];
    }

    /**
     * Returns weekly referral stats.
     *
     * @param \Illuminate\Support\Collection $referrals
     * @return array
     */
    public function getWeeklyStats($referrals)
    {
        $now = now();
        $weeklyReferrals = $referrals->filter(fn($r) => $r->created_at->greaterThanOrEqualTo($now->copy()->subWeek()));

        $totalReferralsCount = $weeklyReferrals->count();
        $totalOrdersAmount = $weeklyReferrals->sum(fn($r) => $r->referrerOrders->sum('amount'));

        $days = collect(range(0, 6))->map(function ($i) use ($now) {
            return $now->copy()->subDays($i);
        });

        $chartData = $days->map(function ($date) use ($weeklyReferrals) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dayReferrals = $weeklyReferrals->filter(function ($referral) use ($dayStart, $dayEnd) {
                return $referral->created_at->between($dayStart, $dayEnd);
            });

            return [
                'day' => Jalalian::fromCarbon($date)->format('%A'),
                'referrals_count' => $dayReferrals->count(),
                'referral_orders_amount' => $dayReferrals->sum(fn($r) => $r->referrerOrders->sum('amount')),
            ];
        })->reverse()->values();

        return [
            'total_referrals_count' => (string)$totalReferralsCount,
            'total_referral_orders_amount' => (string)$totalOrdersAmount,
            'chart_data' => $chartData
        ];
    }

    /**
     * Returns daily referral stats.
     *
     * @param \Illuminate\Support\Collection $referrals
     * @return array
     */
    public function getDailyStats($referrals)
    {
        $now = now();
        $dailyReferrals = $referrals->filter(fn($r) => $r->created_at->greaterThanOrEqualTo($now->copy()->subDay()));

        $totalReferralsCount = $dailyReferrals->count();
        $totalOrdersAmount = $dailyReferrals->sum(fn($r) => $r->referrerOrders->sum('amount'));

        $hours = collect(range(0, 23))->map(function ($i) use ($now) {
            return $now->copy()->subHours($i);
        });

        $chartData = $hours->map(function ($date) use ($dailyReferrals) {
            $hourStart = $date->copy()->startOfHour();
            $hourEnd = $date->copy()->endOfHour();

            $hourReferrals = $dailyReferrals->filter(function ($referral) use ($hourStart, $hourEnd) {
                return $referral->created_at->between($hourStart, $hourEnd);
            });

            return [
                'hour' => Jalalian::fromCarbon($date)->format('H:i'),
                'referrals_count' => $hourReferrals->count(),
                'referral_orders_amount' => $hourReferrals->sum(fn($r) => $r->referrerOrders->sum('amount')),
            ];
        })->reverse()->values();

        return [
            'total_referrals_count' => (string)$totalReferralsCount,
            'total_referral_orders_amount' => (string)$totalOrdersAmount,
            'chart_data' => $chartData
        ];
    }
}
