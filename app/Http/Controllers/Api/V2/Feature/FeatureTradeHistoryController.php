<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\FeatureTradeHistoryItemResource;
use App\Models\Feature;
use App\Services\Feature\FeatureTradeHistoryService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureTradeHistoryController extends Controller
{
    public function __construct(
        private readonly FeatureTradeHistoryService $tradeHistoryService,
    ) {
    }

    #[Endpoint(
        title: 'Get feature trade history',
        description: 'Returns a paginated ownership timeline for a feature, sorted newest first (10 items per page). Includes a synthetic `genesis` entry for initial system ownership on the final page. Only the current feature owner may access this endpoint.',
    )]
    #[QueryParameter('page', description: 'Page number (Laravel standard pagination).', type: 'integer', required: false, example: 1)]
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
