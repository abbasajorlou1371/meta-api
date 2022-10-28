<?php

namespace App\Http\Controllers;

use App\Constants\DebtPaymentStatus;
use App\Constants\DebtsReason;
use App\Constants\FamilyMembersType;
use App\Helpers\AssetHelper;
use App\Http\Requests\CreateDynastyRequest;
use App\Http\Resources\DynastyResource;
use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DynastyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateDynastyRequest $request
     * @param Feature $feature
     * @return DynastyResource|JsonResponse
     */
    public function store(Request $request, Feature $feature): DynastyResource|JsonResponse
    {
        if ($request->user()->cannot('createDynasty', $feature)) {
            abort(403, 'این ملک شرایط لازم جهت تاسیس سلسله را ندارد');
        }

        $dynasty = $request->user()->dynasty()->create([
            'feature_id' => $feature->id,
        ]);

        $family = $dynasty->family()->create();

        $family->familyMembers()->create([
            'user_id' => $request->user()->id,
            'relation' => FamilyMembersType::OWNER
        ]);

        return DynastyResource::make($dynasty);
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function updateDynastyFeature(Request $request)
    {
        $feature = auth()->user()->dynasty->feature;
        if ($request->get('feature_id') == $feature->id) {
            return \response()->json([
                'message' => 'سلسله شما در حال حاضر بر روی همین ملک قرار دارد'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        if (auth()->user()->dynasty->exists()) {
            if (empty(auth()->user()->dynasty->updated_at)) {
                if (auth()->user()->dynasty->created_at->addMonth(1) > now()) {
                    // dynasty is new and user cant update it without debt
                    auth()->user()->dynasty->update([
                        'feature_id' => $request->get('feature_id'),
                    ]);
                    $debtData = [
                        'reason' => DebtsReason::CHANGEDYNASTYFEATURE,
                        'status' => DebtPaymentStatus::UNPAID
                    ];
                    $userFeatureColor = AssetHelper::getAssetColor($feature);
                    if ($userFeatureColor == 'red') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'red' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    } elseif ($userFeatureColor == 'yellow') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'yellow' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    } elseif ($userFeatureColor == 'blue') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'blue' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    }
                } else {
                    // dynasty is old and user can update it without debt
                    $request->user()->dynasty->update([
                        'feature_id' => $request->get('feature_id'),
                    ]);
                    return \response()->json([
                        'message' => 'سلسله شما با موفقیت جابجا شد'
                    ], Response::HTTP_OK);
                }
            } else {
                if ($request->user()->dynasty->updated_at->addMonth(1) > now()) {
                    // dynasty is old and user cant update it without debt
                    $request->user()->dynasty->update([
                        'feature_id' => $request->get('feature_id'),
                    ]);
                    $debtData = [
                        'reason' => DebtsReason::CHANGEDYNASTYFEATURE,
                        'status' => DebtPaymentStatus::UNPAID
                    ];
                    $userFeatureColor = AssetHelper::getAssetColor($feature);
                    if ($userFeatureColor == 'red') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'red' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    } elseif ($userFeatureColor == 'yellow') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'yellow' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    } elseif ($userFeatureColor == 'blue') {
                        $request->user()->debts()->create(array_merge($debtData, [
                            'blue' => $feature->properties->price_psc * 0.01
                        ]));
                        return \response()->json([
                            'message' => 'سلسله شما با موفقیت جابجا شد'
                        ], Response::HTTP_OK);
                    }
                } else {

                    // dynasty is old and user can update it without debt
                    $request->user()->dynasty->update([
                        'feature_id' => $request->get('feature_id'),
                    ]);
                    return \response()->json([
                        'message' => 'سلسله شما با موفقیت جابجا شد'
                    ], Response::HTTP_OK);
                }
            }
        } else {
            // user has no dynasty for update
            return \response()->json([
                'message' => 'شما ملکی جهت ویرایش ندارید'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
