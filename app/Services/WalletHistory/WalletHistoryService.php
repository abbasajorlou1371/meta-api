<?php

namespace App\Services\WalletHistory;

use App\Models\User;

class WalletHistoryService
{
    public function __construct(
        private readonly PeriodResolver $periodResolver,
        private readonly PrivacyChecker $privacyChecker,
        private readonly IncomeCalculator $incomeCalculator,
        private readonly SpendingCalculator $spendingCalculator,
    ) {
    }

    /**
     * @param  array<int, string>|null  $assets
     * @return array<int, array<string, mixed>>
     */
    public function getSummary(User $user, string $period, ?array $assets = null): array
    {
        $user->loadMissing('wallet', 'settings');

        $resolved = $this->periodResolver->resolve($period);
        $previous = $this->periodResolver->resolvePrevious($period);
        $assets = $assets ?? WalletAsset::ALL;

        $summary = [];

        foreach ($assets as $asset) {
            if (! $this->privacyChecker->isVisible($user, $asset)) {
                $summary[] = [
                    'asset' => $asset,
                    'privacy_restricted' => true,
                ];

                continue;
            }

            $periodIncome = $this->incomeCalculator->getTotal(
                $user,
                $asset,
                $resolved['start'],
                $resolved['end']
            );

            $periodSpending = $this->spendingCalculator->getTotal(
                $user,
                $asset,
                $resolved['start'],
                $resolved['end']
            );

            $previousIncome = $this->incomeCalculator->getTotal(
                $user,
                $asset,
                $previous['start'],
                $previous['end']
            );

            $netChange = $periodIncome - $periodSpending;
            $growthPercent = $this->calculateGrowthPercent($netChange, $previousIncome);

            $summary[] = [
                'asset' => $asset,
                'current_balance' => round((float) ($user->wallet?->{$asset} ?? 0), 2),
                'period_income' => round($periodIncome, 2),
                'period_spending' => round($periodSpending, 2),
                'growth_percent' => $growthPercent,
                'direction' => $growthPercent >= 0 ? 'up' : 'down',
                'privacy_restricted' => false,
            ];
        }

        return $summary;
    }

    /**
     * @param  array<int, string>|null  $assets
     * @return array<string, array{income: array<int, array{label: string, amount: float}>, spending: array<int, array{label: string, amount: float}>}>
     */
    public function getChart(User $user, string $period, ?array $assets = null): array
    {
        $user->loadMissing('settings');

        $resolved = $this->periodResolver->resolve($period);
        $assets = $assets ?? WalletAsset::ALL;
        $chart = [];

        foreach ($assets as $asset) {
            if (! $this->privacyChecker->isVisible($user, $asset)) {
                continue;
            }

            $chart[$asset] = [
                'income' => $this->incomeCalculator->getByBuckets($user, $asset, $resolved['buckets']),
                'spending' => $this->spendingCalculator->getByBuckets($user, $asset, $resolved['buckets']),
            ];
        }

        return $chart;
    }

    private function calculateGrowthPercent(float $netChange, float $previousIncome): float
    {
        if ($previousIncome <= 0) {
            return $netChange > 0 ? 100.0 : 0.0;
        }

        return round(($netChange / $previousIncome) * 100, 2);
    }
}
