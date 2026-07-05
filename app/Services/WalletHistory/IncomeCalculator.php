<?php

namespace App\Services\WalletHistory;

use App\Models\BuyFeatureRequest;
use App\Models\FirstOrder;
use App\Models\Feature\FeatureHourlyProfit;
use App\Models\ReferralOrderHistory;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeCalculator
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
        return $this->depositIncome($user, $asset, $start, $end)
            + $this->hourlyProfitIncome($user, $asset, $start, $end)
            + $this->tradeSellIncome($user, $asset, $start, $end)
            + $this->referralIncome($user, $asset, $start, $end)
            + $this->firstOrderBonusIncome($user, $asset, $start, $end)
            + $this->levelPrizeIncome($user, $asset, $start, $end);
    }

    private function depositIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        return (float) Transaction::query()
            ->where('user_id', $user->id)
            ->where('asset', $asset)
            ->where('action', 'deposit')
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function hourlyProfitIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        return (float) FeatureHourlyProfit::query()
            ->where('user_id', $user->id)
            ->where('asset', $asset)
            ->where('is_active', true)
            ->whereBetween('updated_at', [$start, $end])
            ->sum('amount');
    }

    private function tradeSellIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if (! in_array($asset, ['psc', 'irr'], true)) {
            return 0.0;
        }

        $column = $asset === 'psc' ? 'psc_amount' : 'irr_amount';

        return (float) Trade::query()
            ->where('seller_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum($column);
    }

    private function referralIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        if ($asset !== 'psc') {
            return 0.0;
        }

        return (float) ReferralOrderHistory::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function firstOrderBonusIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        return (float) FirstOrder::query()
            ->where('user_id', $user->id)
            ->where('type', $asset)
            ->whereBetween('created_at', [$start, $end])
            ->sum('bonus');
    }

    private function levelPrizeIncome(User $user, string $asset, Carbon $start, Carbon $end): float
    {
        $column = match ($asset) {
            'psc', 'blue', 'red', 'yellow', 'satisfaction', 'effect' => $asset,
            default => null,
        };

        if ($column === null) {
            return 0.0;
        }

        $records = DB::table('recieved_level_prizes')
            ->join('level_prizes', 'level_prizes.id', '=', 'recieved_level_prizes.level_prize_id')
            ->where('recieved_level_prizes.user_id', $user->id)
            ->whereBetween('recieved_level_prizes.created_at', [$start, $end])
            ->get(['level_prizes.psc', 'level_prizes.blue', 'level_prizes.red', 'level_prizes.yellow', 'level_prizes.satisfaction', 'level_prizes.effect']);

        if ($records->isEmpty()) {
            return 0.0;
        }

        if ($asset === 'psc') {
            $pscRate = Variable::getRate('psc') ?: 1;

            return (float) $records->sum(fn ($record) => ($record->psc ?? 0) / $pscRate);
        }

        return (float) $records->sum($column);
    }
}
