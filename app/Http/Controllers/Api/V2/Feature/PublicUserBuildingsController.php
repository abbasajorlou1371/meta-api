<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UserBuildingResource;
use App\Models\User;
use App\Services\UserFeatures\UserBuildingsService;
use App\Services\WalletHistory\PeriodResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicUserBuildingsController extends Controller
{
    public function __construct(
        private readonly UserBuildingsService $userBuildingsService,
    ) {
    }

    public function summary(Request $request, User $user): JsonResponse
    {
        return response()->json(
            $this->userBuildingsService->getSummary(
                $user,
                $this->resolveKarbaris($request)
            )
        );
    }

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
