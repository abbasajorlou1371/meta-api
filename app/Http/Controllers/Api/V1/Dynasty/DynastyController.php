<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Http\Requests\CreateDynastyRequest;
use App\Http\Resources\Dynasty\DynastyResource;
use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dynasty\IntroductionPrizeResource;
use App\Models\Dynasty\Dynasty;
use App\Models\Dynasty\DynastyPrize;
use App\Models\LockedFeature;
use App\Notifications\DynastyCreatedNotification;
use App\Notifications\DynastyFeatureChangedNotification;

class DynastyController extends Controller
{
    public function __construct()
    {
        $this->middleware('account.security')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return DynastyResource|JsonResponse
     */
    public function index(Request $request): JsonResponse|DynastyResource
    {
        // Check if the user has a dynasty
        $dynasty = Dynasty::whereBelongsTo($request->user())->with([
            'family',
            'family.familyMembers',
            'feature',
            'user'
        ])->first();

        // If the user doesn't have a dynasty, return the features that the user can choose from
        if (is_null($dynasty)) {
            // Get all residentil features of the user
            $features =  $request->user()->features
                ->reject(function ($feature) {
                    return $feature->properties->karbari !== 'm';
                })
                ->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'properties_id' => $feature->properties->id,
                        'stability' => $feature->properties->stability
                    ];
                });

            // Return the features
            return response()->json([
                'data' => [
                    'user-has-dynasty' => false,
                    'features' => $features,
                    'prizes' => IntroductionPrizeResource::collection(DynastyPrize::all())
                ]
            ]);
        }

        // If the user has a dynasty, return the dynasty
        return new DynastyResource($dynasty);
    }

    /**
     * Create a new dynasty
     *
     * @param CreateDynastyRequest $request
     * @param Feature $feature
     * @return DynastyResource|JsonResponse
     */
    public function store(Request $request, Feature $feature): DynastyResource|JsonResponse
    {
        // Check if the user has a dynasty
        $this->authorize('create', [Dynasty::class, $feature]);

        // Create a new dynasty
        $dynasty = $request->user()->dynasty()->create([
            'feature_id' => $feature->id,
        ]);

        // Create a new family for the dynasty
        $family = $dynasty->family()->create();

        // Add the user to the family
        $family->familyMembers()->create([
            'user_id' => $request->user()->id,
            'relationship' => 'owner'
        ]);

        // Update the dynasty's feature label
        $request->user()->notify(new DynastyCreatedNotification($feature->properties->id));

        // Return the dynasty
        return new DynastyResource($dynasty);
    }

    /**
     * Update the dynasty's feature
     *
     * @param Dynasty $dynasty
     * @param Feature $feature
     * @param Request $request
     * @return DynastyResource
     */
    public function update(Dynasty $dynasty, Feature $feature, Request $request)
    {
        // Check if the user is authorized to update the dynasty's feature
        $this->authorize('update', [$dynasty, $feature]);

        // Get the dynasty's current feature
        $currentFeature = $dynasty->feature;

        // Update the dynasty's feature
        $dynasty->update(['feature_id' => $feature->id]);

        // Check if the dynasty's current feature is updated in the last 30 days
        if ($dynasty->updated_at->diffInDays(now()) < 30) {
            // If the dynasty's current feature is updated in the last 30 days, add a debt to the user
            $request->user()->debts()->create([
                $currentFeature->getColor() => $currentFeature->properties->stability * 0.01,
                'reason' => 'update-dynasty-feature',
            ]);

            // Set the dynasty's current feature label to 'locked'
            $currentFeature->properties->update(['label' => 'locked']);

            // Create a new locked feature
            LockedFeature::create([
                'feature_id' => $currentFeature->id,
                'reason' => 'dynasty-feature-change',
                'until' => now()->addMonth(),
                'status' => 0,
            ]);
        }

        // Notify the user about the dynasty's feature change
        $request->user()->notify(new DynastyFeatureChangedNotification($feature->properties->id));

        // Return the dynasty
        return new DynastyResource($dynasty->refresh());
    }
}
