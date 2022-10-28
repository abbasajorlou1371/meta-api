<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\SellFeatureRequest;
use Illuminate\Auth\Access\Response;

class SellRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, SellFeatureRequest $sellRequest) {
        return $user->id === $sellRequest->seller_id
        ? Response::allow()
        : Response::deny('این درخواست متعلق به شما نیست');
    }
}
