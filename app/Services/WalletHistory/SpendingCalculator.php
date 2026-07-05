<?php

namespace App\Services\WalletHistory;

use App\Models\BuyFeatureRequest;
use App\Models\Feature\Building;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class SpendingCalculator
{
    /**
     * @param  array<int, array{start: Carbon, end: Carbon, label: string}>  $buckets
     * @return array<int, array{label: string, amount: float}>
     */
    public function getByBuckets(User $user, string $asset, array $buckets): array
    {
        return array_map(function (array $bucket) use ($user, $asset) {
            return [
                'label' => $bucket['label'],
                'amount' => round($this->getTotal($user, $asset, $bucket['start'], $bucket['end']), 2),
            ];
        }, $buckets);
    }

    public function getTotal(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        return $this->tradeBuySpending($user, $asset, $start, $end)
            + $this->buyFeatureSpending($user, $asset, $start, $end)
            + $this->colorWithdrawSpending($user, $asset, $start, $end)
            + $this->satisfactionLaunchSpending($user, $asset, $start, $end);
    }

    private function tradeBuySpending(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if (! in_array($asset, ['psc', 'irr'], true)) {
            return 0.0;
        }

        $column = $asset === 'psc' ? 'psc_amount' : 'irr_amount';

        return (float) Trade::query()
            ->where('buyer_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum($column);
    }

    private function buyFeatureSpending(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if (! in_array($asset, ['psc', 'irr'], true)) {
            return 0.0;
        }

        return (float) Transaction::query()
            ->where('user_id', $user->id)
            ->where('asset', $asset)
            ->where('action', 'withdraw')
            ->where('payable_type', BuyFeatureRequest::class)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function colorWithdrawSpending(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if (! in_array($asset, WalletAsset::COLORS, true)) {
            return 0.0;
        }

        return (float) Transaction::query()
            ->where('user_id', $user->id)
            ->where('asset', $asset)
            ->where('action', 'withdraw')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function satisfactionLaunchSpending(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if ($asset !== 'satisfaction') {
            return 0.0;
        }

        return (float) Building::query()
            ->where('user_id', $user->id)
            ->whereBetween('construction_start_date', [$start, $end])
            ->sum('launched_satisfaction');
    }
}
