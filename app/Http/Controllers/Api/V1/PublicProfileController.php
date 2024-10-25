<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\PublicProfile\PersonalInfo;

class PublicProfileController extends Controller
{
    public function home(User $user)
    {
        $user->load(['kyc', 'personalInfo', 'profilePhotos', 'settings:id,user_id,privacy']);
        return new PersonalInfo($user);
    }
}
