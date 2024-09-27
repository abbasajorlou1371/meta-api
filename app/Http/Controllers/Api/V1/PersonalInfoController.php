<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePersonalInfoRequest;
use App\Models\User\PersonalInfo;
use Illuminate\Http\Request;

class PersonalInfoController extends Controller
{

    /**
     * Show user's personal info.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $personalInfo = request()->user()->personalInfo;

        return response()->json([
            'data' => is_null($personalInfo) ? [] :
                [
                    'occupation' => $personalInfo->occupation,
                    'education' => $personalInfo->education,
                    'memory' => $personalInfo->memory,
                    'loved_city' => $personalInfo->loved_city,
                    'loved_country' => $personalInfo->loved_country,
                    'loved_language' => $personalInfo->loved_language,
                    'problem_solving' => $personalInfo->problem_solving,
                    'prediction' => $personalInfo->prediction,
                    'about' => $personalInfo->about,
                    'passions' => $personalInfo->passions,
                ]
        ]);
    }

    /**
     * Update user's personal info.
     *
     * @param  \Illuminate\Http\UpdatePersonalInfoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePersonalInfoRequest $request)
    {
        PersonalInfo::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        return response()->json([], 204);
    }
}
