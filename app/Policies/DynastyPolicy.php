<?php

namespace App\Policies;

use App\Models\Dynasty\Dynasty;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DynastyPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        if(! $user->verified()) {
            return Response::deny('شما برای تاسیس سلسله باید احراز هویت مرحله 2 را انجام دهید', 403);
        }

        if(! empty($user->dynasty)) {
            return Response::deny('شما در حال حاظر سلسله دارید و مجاز به تاسیس سلسله جدید نیستید', 403);
        }
        return true;
    }

    public function updateDynastyFeature(User $user, Dynasty $dynasty, Feature $feature)
    {
        if($feature->hasPendingRequests()) return false;
        if($feature->owner_id !== $user->id) return false;
        if($feature->id === $dynasty->feature_id) return false;
        return $user->id === $dynasty->user_id;
    }
}
