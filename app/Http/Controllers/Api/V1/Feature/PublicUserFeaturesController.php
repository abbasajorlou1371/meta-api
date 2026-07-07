<?php

namespace App\Http\Controllers\Api\V1\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicFeatureListResource;
use App\Models\User;
use App\Services\UserFeatures\UserFeaturesService;
use App\Services\WalletHistory\PeriodResolver;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicUserFeaturesController extends Controller
{
    public function __construct(
        private readonly UserFeaturesService $userFeaturesService,
    ) {
    }

    #[Endpoint(
        title: 'Get public user features summary',
        description: 'Per-karbari summary cards with current inventory, bought count, and sold count for the selected period. Public endpoint — no authentication required.',
    )]
    #[QueryParameter('period', description: 'Time window for bought/sold counts: `daily`, `weekly`, `monthly`, or `yearly`. Defaults to `daily`.', type: 'string', required: false, example: 'weekly')]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes.', type: 'string', required: false)]
    public function summary(Request $request, User $user): JsonResponse
    {
        $period = $this->resolvePeriod($request);

        return response()->json(
            $this->userFeaturesService->getSummary(
                $user,
                $period,
                $this->resolveKarbaris($request)
            )
        );
    }

    #[Endpoint(
        title: 'Get public user features chart',
        description: 'Dual-line chart of features bought vs sold over time for the selected period. Public endpoint — no authentication required.',
    )]
    #[QueryParameter('period', description: 'Time window: `daily`, `weekly`, `monthly`, or `yearly`. Defaults to `daily`.', type: 'string', required: false, example: 'weekly')]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes.', type: 'string', required: false)]
    public function chart(Request $request, User $user): JsonResponse
    {
        $period = $this->resolvePeriod($request);

        return response()->json(
            $this->userFeaturesService->getChart(
                $user,
                $period,
                $this->resolveKarbaris($request)
            )
        );
    }

    #[Endpoint(
        title: 'List public user features',
        description: 'Paginated searchable feature list with map marker coordinates. Public endpoint — no authentication required.',
    )]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes.', type: 'string', required: false)]
    #[QueryParameter('search', description: 'Search by feature properties ID (partial match).', type: 'string', required: false)]
    #[QueryParameter('per_page', description: 'Items per page.', type: 'integer', required: false, example: 15)]
    #[QueryParameter('page', description: 'Page number.', type: 'integer', required: false, example: 1)]
    public function index(Request $request, User $user): JsonResponse
    {
        $result = $this->userFeaturesService->getFeatures(
            $user,
            $this->resolveKarbaris($request),
            $request->input('search'),
            (int) $request->input('per_page', 15)
        );

        return PublicFeatureListResource::collection($result['features'])
            ->additional([
                'map_markers' => $result['map_markers'],
            ])
            ->response();
    }

    private function resolvePeriod(Request $request): string
    {
        $period = $request->input('period', 'daily');

        return in_array($period, PeriodResolver::PERIODS, true) ? $period : 'daily';
    }

    /**
     * @return array<string>|null
     */
    private function resolveKarbaris(Request $request): ?array
    {
        $karbaris = $request->input('karbari');

        if ($karbaris === null) {
            return null;
        }

        return is_array($karbaris) ? $karbaris : [$karbaris];
    }
}
