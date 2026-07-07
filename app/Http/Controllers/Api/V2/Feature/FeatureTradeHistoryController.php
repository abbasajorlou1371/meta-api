<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\FeatureTradeHistoryItemResource;
use App\Models\Feature;
use App\Services\Feature\FeatureTradeHistoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureTradeHistoryController extends Controller
{
    public function __construct(
        private readonly FeatureTradeHistoryService $tradeHistoryService,
    ) {
    }

    public function index(Feature $feature): AnonymousResourceCollection
    {
        $this->authorize('viewTradeHistory', $feature);

        $history = $this->tradeHistoryService->paginate(
            $feature,
            request()->integer('page', 1),
        );

        return FeatureTradeHistoryItemResource::collection($history);
    }
}
