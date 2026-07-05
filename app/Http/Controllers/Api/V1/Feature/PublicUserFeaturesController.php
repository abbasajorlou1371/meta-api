<?php

namespace App\Http\Controllers\Api\V1\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicFeatureListResource;
use App\Models\User;
use App\Services\UserFeatures\UserFeaturesService;
use App\Services\WalletHistory\PeriodResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicUserFeaturesController extends Controller
{
    public function __construct(
        private readonly UserFeaturesService $userFeaturesService,
    ) {
    }

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
