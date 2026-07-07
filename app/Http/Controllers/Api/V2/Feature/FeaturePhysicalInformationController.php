<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Feature\UpsertFeaturePhysicalInformationRequest;
use App\Http\Resources\V2\FeaturePhysicalInformationResource;
use App\Models\Feature;
use App\Services\Feature\FeaturePhysicalInformationService;
use Dedoc\Scramble\Attributes\Endpoint;
use Illuminate\Http\JsonResponse;

class FeaturePhysicalInformationController extends Controller
{
    public function __construct(
        private readonly FeaturePhysicalInformationService $physicalInformationService,
    ) {
    }

    #[Endpoint(
        title: 'Get feature physical information',
        description: 'Returns the physical information section for a feature. Only the feature owner may access this endpoint. Returns `data: null` when no record exists yet.',
    )]
    public function show(Feature $feature): JsonResponse|FeaturePhysicalInformationResource
    {
        $this->authorize('viewPhysicalInformation', $feature);

        $physicalInformation = $this->physicalInformationService->get($feature);

        if ($physicalInformation === null) {
            return response()->json([
                'data' => null,
            ]);
        }

        return new FeaturePhysicalInformationResource($physicalInformation);
    }

    #[Endpoint(
        title: 'Create or update feature physical information',
        description: 'Creates physical information when none exists, or fully replaces the stored values. Provide either `isic_code_id` or `activity`, not both. Only the feature owner may write.',
    )]
    public function upsert(
        UpsertFeaturePhysicalInformationRequest $request,
        Feature $feature,
    ): JsonResponse {
        $physicalInformation = $this->physicalInformationService->upsert(
            $feature,
            $request->validated(),
        );

        return (new FeaturePhysicalInformationResource($physicalInformation))
            ->response()
            ->setStatusCode(200);
    }
}
