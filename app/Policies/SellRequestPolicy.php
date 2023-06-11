<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\SellFeatureRequest;
use Illuminate\Auth\Access\Response;

class SellRequestPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, SellFeatureRequest $sellRequest) {
        return $sellRequest->seller->is($user);
    }
}
