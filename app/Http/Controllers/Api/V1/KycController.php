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

        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateKycRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateKycRequest $request)
    {
        $kycData = $request->only(['fname', 'lname', 'melli_code', 'birthdate', 'province', 'verify_text_id', 'status', 'errors', 'gender']);

        if ($request->hasFile('melli_card')) {
            $storedPath = $request->file('melli_card')->store('kyc', 'public');
            
            // Remove execution permissions from uploaded file (security measure)
            $fullPath = storage_path('app/public/' . $storedPath);
            if (file_exists($fullPath)) {
                chmod($fullPath, 0644);
            }
            
            $kycData['melli_card'] = url('uploads/' . $storedPath);
        }

        if ($request->has('video')) {
            $originalPath = storage_path('app/' . $request->video['path'] . '/' . $request->video['name']);
            $newPath = storage_path('app/public/kyc/' . $request->video['name']);
            
            // Ensure directory exists
            if (!is_dir(dirname($newPath))) {
                mkdir(dirname($newPath), 0755, true);
            }
            
            rename($originalPath, $newPath);
            
            // Remove execution permissions from uploaded file (security measure)
            if (file_exists($newPath)) {
                chmod($newPath, 0644);
            }
            
            $kycData['video'] = url('uploads/kyc/' . $request->video['name']);
        }

        $kyc = Kyc::updateOrCreate(
            ['user_id' => $request->user()->id],
            $kycData
        );

        return new KycResource($kyc->refresh());
    }
}
