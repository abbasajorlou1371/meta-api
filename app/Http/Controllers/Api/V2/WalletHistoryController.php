<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletHistory\WalletHistoryChartRequest;
use App\Http\Requests\WalletHistory\WalletHistorySummaryRequest;
use App\Http\Resources\WalletHistory\ChartResource;
use App\Http\Resources\WalletHistory\SummaryResource;
use App\Models\User;
use App\Services\WalletHistory\WalletHistoryService;
use Dedoc\Scramble\Attributes\Endpoint;

class WalletHistoryController extends Controller
{
    public function __construct(
        private readonly WalletHistoryService $walletHistoryService,
    ) {
    }

    #[Endpoint(
        title: 'Get wallet asset history summary',
        description: 'Asset cards with current balance, period income/spending, and growth percentage. Public endpoint — resolves the user by public code and respects privacy settings.',
    )]
    public function summary(WalletHistorySummaryRequest $request, User $user): SummaryResource
    {
        $summary = $this->walletHistoryService->getSummary(
            $user,
            $request->validated('period'),
            $request->validated('assets')
        );

        return new SummaryResource($summary);
    }

    #[Endpoint(
        title: 'Get wallet asset history chart',
        description: 'Time-series income and spending lines per asset for the selected period. Public endpoint — resolves the user by public code and respects privacy settings.',
    )]
    public function chart(WalletHistoryChartRequest $request, User $user): ChartResource
    {
        $chart = $this->walletHistoryService->getChart(
            $user,
            $request->validated('period'),
            $request->validated('assets')
        );

        return new ChartResource($chart);
    }
}
