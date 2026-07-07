<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Feature\UpsertFeaturePhysicalInformationRequest;
use App\Http\Resources\V2\FeaturePhysicalInformationResource;
use App\Models\Feature;
use App\Services\Feature\FeaturePhysicalInformationService;
use Illuminate\Http\JsonResponse;

class FeaturePhysicalInformationController extends Controller
{
    public function __construct(
        private readonly FeaturePhysicalInformationService $physicalInformationService,
    ) {
    }

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
