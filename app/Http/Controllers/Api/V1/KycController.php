<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateKycRequest;
use App\Http\Resources\KycResource;
use App\Models\Kyc;

class KycController extends Controller
{
    /**
     * Get the current user's kyc info.
     *
     * @return KycResource|null
     */
    public function show()
    {
        $kyc = request()->user()->kyc;

        if ($kyc && request()->user()->can('view', $kyc)) {
            return new KycResource($kyc);
        }

        return response()->json(null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateKycRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateKycRequest $request)
    {
        $kyc = $request->user()->kyc ?? new Kyc();

        if ($request->hasFile('melli_card')) {
            $kyc->melli_card = url('uploads/' . $request->file('melli_card')->store('kyc', 'public'));
        }

        if ($request->has('video')) {
            $originalPath = storage_path('app/' . $request->video['path'] . '/' . $request->video['name']);

            rename($originalPath, storage_path('app/public/kyc/' . $request->video['name']));

            $kyc->video = url('uploads/kyc/' . $request->video['name']);
        }

        $kyc->errors = null;
        $kyc->status = 0;

        $kyc->fill($request->only(['fname', 'lname', 'melli_code', 'birthdate', 'province', 'verify_text_id']));

        $kyc->save();

        return response()->json([]);
    }
}
