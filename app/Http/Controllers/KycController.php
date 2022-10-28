<?php

namespace App\Http\Controllers;

use App\Http\Requests\KycRequest;
use App\Http\Resources\KycResource;
use App\Models\Kyc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('sanctum')->user();
    }

    /**
     * @param Kyc $kyc
     * @return KycResource
     */
    public function show(Kyc $kyc): KycResource
    {
        return new KycResource($kyc);
    }

    /**
     * @param KycRequest $request
     * @return KycResource
     */
    public function store(KycRequest $request): KycResource
    {
        $melliCardNameToStore = env('FTP_ENDPOINT') .
            $request->file('melli_card')->store('user/kyc/' . $this->user->id);
        $provePictureNameToStore = env('FTP_ENDPOINT') .
            $request->file('prove_picture')->store('user/kyc/' . $this->user->id);

        if ($request->hasFile('resume')) {
            $resumeNameToStore = env('FTP_ENDPOINT') .
                $request->file('resume')->store('user/kyc/' . $this->user->id);
        } else {
            $resumeNameToStore = "";
        }

        $kyc = Kyc::create([
            'shaba' => $request->getShaba(),
            'bank' => $request->getBank(),
            'melli_card' => $melliCardNameToStore,
            'prove_picture' => $provePictureNameToStore,
            'resume' => $resumeNameToStore,
            'fname' => $request->getFirstName(),
            'lname' => $request->getLastName(),
            'father_name' => $request->getFatherName(),
            'melli_code' => $request->getMeliCode(),
            'birthdate' => convertDateToCarbon($request->getBirthdate()),
            'province' => $request->getProvience(),
            'city' => $request->getCity(),
            'number' => $request->getNumber(),
            'postal_code' => $request->getPostalCode(),
            'address' => $request->getAddress(),
            'site' => $request->getSite(),
            'user_id' => $this->user->id,
        ]);

        $kyc->message = 'درخواست احراز هویت شما با موفقیت ثبت شد';
        return new KycResource($kyc);
    }


    /**
     * @param KycRequest $request
     * @param Kyc $kyc
     * @return KycResource
     */
    public function update(KycRequest $request, Kyc $kyc): KycResource
    {
        if ($request->hasFile('melli_card')) {
            $kyc->melli_card = env('FTP_ENDPOINT') .
                $request->file('melli_card')->store('user/kyc/' . $this->user->id);
        }

        if ($request->hasFile('prove_picture')) {
            $kyc->prove_picture = env('FTP_ENDPOINT') .
                $request->file('prove_picture')->store('user/kyc/' . $this->user->id);
        }

        if ($request->hasFile('resume')) {
            $kyc->resume = env('FTP_ENDPOINT') .
                $request->file('resume')->store('user/kyc/' . $this->user->id);
        }

        $kyc->update([
            'shaba' => $request->getShaba(),
            'bank' => $request->getBank(),
            'melli_card' => $kyc->melli_card,
            'prove_picture' => $kyc->prove_picture,
            'resume' => $kyc->resume,
            'fname' => $request->getFirstName(),
            'lname' => $request->getLastName(),
            'father_name' => $request->getFatherName(),
            'melli_code' => $request->getMeliCode(),
            'birthdate' => convertDateToCarbon($request->getBirthdate()),
            'province' => $request->getProvience(),
            'city' => $request->getCity(),
            'number' => $request->getNumber(),
            'postal_code' => $request->getPostalCode(),
            'address' => $request->getAddress(),
            'site' => $request->getSite(),
        ]);

        $kyc->message = 'درخواست احراز بروزرسانی شد';

        return new KycResource($kyc);
    }

    /**
     * @param Kyc $kyc
     * @return JsonResponse
     */
    public function destroy(Kyc $kyc): JsonResponse
    {
        $kyc->delete();
        return response()->json([
            'success' => 'احراز هویت حذف شد'
        ]);
    }
}
