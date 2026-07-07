<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UserBuildingResource;
use App\Models\User;
use App\Services\UserFeatures\UserBuildingsService;
use App\Services\WalletHistory\PeriodResolver;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicUserBuildingsController extends Controller
{
    public function __construct(
        private readonly UserBuildingsService $userBuildingsService,
    ) {
    }

    #[Endpoint(
        title: 'Get public user buildings summary',
        description: 'Per-karbari summary cards with the total count of completed buildings owned by the user. Public endpoint — no authentication required. Respects the user\'s privacy settings.',
    )]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes (e.g. `m`, `t`). Omit to include all displayable karbari types.', type: 'string', required: false)]
    public function summary(Request $request, User $user): JsonResponse
    {
        return response()->json(
            $this->userBuildingsService->getSummary(
                $user,
                $this->resolveKarbaris($request)
            )
        );
    }

    #[Endpoint(
        title: 'Get public user buildings chart',
        description: 'Line chart of completed buildings over time, bucketed by the selected period. Buildings are grouped by `construction_end_date`. Public endpoint — no authentication required.',
    )]
    #[QueryParameter('period', description: 'Time window: `daily`, `weekly`, `monthly`, or `yearly`. Invalid values fall back to `daily`.', type: 'string', required: false, example: 'weekly')]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes.', type: 'string', required: false)]
    public function chart(Request $request, User $user): JsonResponse
    {
        $period = $this->resolvePeriod($request);

        return response()->json(
            $this->userBuildingsService->getChart(
                $user,
                $period,
                $this->resolveKarbaris($request)
            )
        );
    }

    #[Endpoint(
        title: 'List public user buildings',
        description: 'Paginated list of completed buildings on the user\'s features (10 per page). Public endpoint — no authentication required.',
    )]
    #[QueryParameter('karbari[]', description: 'Filter to specific karbari codes.', type: 'string', required: false)]
    #[QueryParameter('page', description: 'Page number.', type: 'integer', required: false, example: 1)]
    public function index(Request $request, User $user): JsonResponse
    {
        $buildings = $this->userBuildingsService->getBuildings(
            $user,
            $this->resolveKarbaris($request),
            10
        );

        return UserBuildingResource::collection($buildings)->response();
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
