<?php

namespace App\Policies;

use App\Models\BuyFeatureRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BuyFeatureRequestPolicy
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



    public function delete(User $user, BuyFeatureRequest $buyFeatureRequest) {
        return $user->id === $buyFeatureRequest->buyer_id;
    }

    public function reject(User $user, BuyFeatureRequest $buyFeatureRequest) {
        return $user->id === $buyFeatureRequest->seller_id;
    }

    public function accept(User $user, BuyFeatureRequest $buyFeatureRequest) {
        return $user->id !== $buyFeatureRequest->seller_id
               || $buyFeatureRequest->status === 1
               ? Response::deny('درخواست معتبر نمی باشد', 404)
               : Response::allow();
    }


}
